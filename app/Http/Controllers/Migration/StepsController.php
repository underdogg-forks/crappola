<?php

namespace App\Http\Controllers\Migration;

use Carbon\Carbon;
use App\Http\Controllers\BaseController;
use App\Http\Requests\MigrationAuthRequest;
use App\Http\Requests\MigrationCompaniesRequest;
use App\Http\Requests\MigrationEndpointRequest;
use App\Http\Requests\MigrationTypeRequest;
use App\Jobs\HostedMigration;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\AccountGatewayToken;
use App\Models\Client;
use App\Services\Migration\AuthService;
use App\Services\Migration\CompanyService;
use App\Services\Migration\CompleteService;
use App\Traits\GenerateMigrationResources;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use ZipArchive;

class StepsController extends BaseController
{
    use GenerateMigrationResources;

    private array $access = [
        'auth' => [
            'steps'    => ['MIGRATION_TYPE'],
            'redirect' => '/migration/start',
        ],
        'endpoint' => [
            'steps'    => ['MIGRATION_TYPE'],
            'redirect' => '/migration/start',
        ],
        'companies' => [
            'steps'    => ['MIGRATION_TYPE', 'MIGRATION_ACCOUNT_TOKEN'],
            'redirect' => '/migration/auth',
        ],
    ];

    public function __construct()
    {
        $this->middleware('migration');
    }

    public function start(): Factory|View
    {
        if (Utils::isNinja()) {
            session()->put('MIGRATION_ENDPOINT', 'https://v5-app1.invoicing.co');
            session()->put('MIGRATION_ACCOUNT_TOKEN', '');
            session()->put('MIGRATION_API_SECRET');

            return $this->companies();
        }

        return view('migration.start');
    }

    public function import(): Factory|Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('migration.import');
    }

    public function download(): Factory|View
    {
        return view('migration.download');
    }

    public function handleType(MigrationTypeRequest $request): Application|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        session()->put('MIGRATION_TYPE', $request->option);

        if ($request->option == 0 || $request->option == '0') {
            return redirect(
                url('/migration/companies?hosted=true')
            );
        }

        return redirect(
            url('/migration/endpoint')
        );
    }

    public function forwardUrl(Request $request)
    {
        if (Utils::isNinjaProd()) {
            return $this->autoForwardUrl();
        }

        $rules = [
            'url' => 'nullable|url',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $account_settings = Auth::user()->account->account_email_settings;

        $account_settings->is_disabled = mb_strlen($request->input('url')) != 0;

        $account_settings->forward_url_for_v5 = rtrim($request->input('url'), '/');
        $account_settings->save();

        return back();
    }

    public function disableForwarding()
    {
        $account = Auth::user()->account;

        $account_settings = $account->account_email_settings;
        $account_settings->forward_url_for_v5 = '';
        $account_settings->is_disabled = false;
        $account_settings->save();

        return back();
    }

    public function endpoint()
    {
        if ($this->shouldGoBack('endpoint')) {
            return redirect(
                url($this->access['endpoint']['redirect'])
            );
        }

        return view('migration.endpoint');
    }

    public function handleEndpoint(MigrationEndpointRequest $request)
    {
        if ($this->shouldGoBack('endpoint')) {
            return redirect(
                url($this->access['endpoint']['redirect'])
            );
        }

        session()->put('MIGRATION_ENDPOINT', rtrim($request->endpoint, '/'));

        return redirect(
            url('/migration/auth')
        );
    }

    public function auth()
    {
        if ($this->shouldGoBack('auth')) {
            return redirect(
                url($this->access['auth']['redirect'])
            );
        }

        return view('migration.auth');
    }

    public function handleAuth(MigrationAuthRequest $request)
    {
        if ($this->shouldGoBack('auth')) {
            return redirect(
                url($this->access['auth']['redirect'])
            );
        }

        if (auth()->user()->email !== $request->email) {
            return back()->with('responseErrors', [trans('texts.cross_migration_message')]);
        }

        $authentication = (new AuthService($request->email, $request->password, $request->has('api_secret') ? $request->api_secret : null))
            ->endpoint(session('MIGRATION_ENDPOINT'))
            ->start();

        if ($authentication->isSuccessful()) {
            session()->put('MIGRATION_ACCOUNT_TOKEN', $authentication->getAccountToken());
            session()->put('MIGRATION_API_SECRET', $authentication->getApiSecret());

            return redirect(
                url('/migration/companies')
            );
        }

        return back()->with('responseErrors', $authentication->getErrors());
    }

    public function companies(): \Illuminate\Contracts\View\View|Application|Factory|JsonResponse|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->shouldGoBack('companies')) {
            return redirect(
                url($this->access['companies']['redirect'])
            );
        }

        $companyService = (new CompanyService())
            ->start();

        if ($companyService->isSuccessful()) {
            return view('migration.companies', ['companies' => $companyService->getCompanies()]);
        }

        return response()->json([
            'message' => 'Oops, looks like something failed. Please try again.',
        ], 500);
    }

    public function handleCompanies(MigrationCompaniesRequest $request): \Illuminate\Contracts\View\View|Application|Factory|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->shouldGoBack('companies')) {
            return redirect(
                url($this->access['companies']['redirect'])
            );
        }

        if (Utils::isNinja()) {
            $this->dispatch(new HostedMigration(auth()->user(), $request->all(), config('database.default')));

            return view('migration.completed');
        }

        $completeService = (new CompleteService(session('MIGRATION_ACCOUNT_TOKEN')));

        try {
            $migrationData = $this->generateMigrationData($request->all());

            $completeService->data($migrationData)
                ->endpoint(session('MIGRATION_ENDPOINT'))
                ->start();
        } catch (Exception $exception) {
            info($exception->getMessage());

            return view('migration.completed', ['customMessage' => $exception->getMessage()]);
        }

        if ($completeService->isSuccessful()) {
            return view('migration.completed');
        }

        return view('migration.completed', ['customMessage' => $completeService->getErrors()[0]]);
    }

    public function completed(): Factory|Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('migration.completed');
    }

    /**
     * ==================================
     * Rest of functions that are used as 'actions', not controller methods.
     * ==================================.
     */
    public function shouldGoBack(string $step): bool
    {
        $redirect = true;

        foreach ($this->access[$step]['steps'] as $step) {
            $redirect = ! session()->has($step);
        }

        return $redirect;
    }

    public function generateMigrationData(array $data): array
    {
        set_time_limit(0);

        $migrationData = [];

        foreach ($data['companies'] as $company) {
            $account = Account::where('account_key', $company['id'])->firstOrFail();

            $this->account = $account;

            $date = Carbon::now()->format('Y-m-d');
            $accountKey = $this->account->account_key;

            $output = fopen('php://output', 'w') || Utils::fatalError();

            $fileName = sprintf('%s-%s-invoiceninja', $accountKey, $date);

            $localMigrationData['data'] = [
                'account'               => $this->getAccount(),
                'company'               => $this->getCompany(),
                'users'                 => $this->getUsers(),
                'tax_rates'             => $this->getTaxRates(),
                'payment_terms'         => $this->getPaymentTerms(),
                'clients'               => $this->getClients(),
                'company_gateways'      => $this->getCompanyGateways(),
                'client_gateway_tokens' => $this->getClientGatewayTokens(),
                'vendors'               => $this->getVendors(),
                'projects'              => $this->getProjects(),
                'products'              => $this->getProducts(),
                'credits'               => $this->getCreditsNotes(),
                'invoices'              => $this->getInvoices(),
                'recurring_expenses'    => $this->getRecurringExpenses(),
                'recurring_invoices'    => $this->getRecurringInvoices(),
                'quotes'                => $this->getQuotes(),
                'payments'              => $this->getPayments(),
                'documents'             => $this->getDocuments(),
                'expense_categories'    => $this->getExpenseCategories(),
                'task_statuses'         => $this->getTaskStatuses(),
                'expenses'              => $this->getExpenses(),
                'tasks'                 => $this->getTasks(),
                'ninja_tokens'          => $this->getNinjaToken(),
            ];

            $localMigrationData['force'] = array_key_exists('force', $company);

            Storage::makeDirectory('migrations');
            $file = Storage::path(sprintf('migrations/%s.zip', $fileName));

            ksort($localMigrationData);

            $zip = new ZipArchive();
            $zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFromString('migration.json', json_encode($localMigrationData, JSON_PRETTY_PRINT));
            $zip->close();

            $localMigrationData['file'] = $file;

            $migrationData[] = $localMigrationData;
        }

        return $migrationData;

        // header('Content-Type: application/zip');
        // header('Content-Length: ' . filesize($file));
        // header("Content-Disposition: attachment; filename={$fileName}.zip");
    }

    private function autoForwardUrl(): RedirectResponse
    {
        $url = 'https://invoicing.co/api/v1/confirm_forwarding';
        // $url = 'http://devhosted.test:8000/api/v1/confirm_forwarding';

        $headers = [
            'X-API-HOSTED-SECRET' => config('ninja.ninja_hosted_secret'),
            'X-Requested-With'    => 'XMLHttpRequest',
            'Content-Type'        => 'application/json',
        ];

        $account = Auth::user()->account;
        $gateway_reference = '';

        $ninja_client = Client::where('public_id', $account->id)->first();

        if ($ninja_client) {
            $agt = AccountGatewayToken::where('client_id', $ninja_client->id)->first();

            if ($agt) {
                $gateway_reference = $agt->token;
            }
        }

        $body = [
            'account_key'       => $account->account_key,
            'email'             => $account->getPrimaryUser()->email,
            'plan'              => $account->company->plan,
            'plan_term'         => $account->company->plan_term,
            'plan_started'      => $account->company->plan_started,
            'plan_paid'         => $account->company->plan_paid,
            'plan_expires'      => $account->company->plan_expires,
            'trial_started'     => $account->company->trial_started,
            'trial_plan'        => $account->company->trial_plan,
            'plan_price'        => $account->company->plan_price,
            'num_users'         => $account->company->num_users,
            'gateway_reference' => $gateway_reference,
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
        ]);

        $response = $client->post($url, [
            RequestOptions::JSON            => $body,
            RequestOptions::ALLOW_REDIRECTS => false,
        ]);

        if ($response->getStatusCode() == 401) {
            info('autoForwardUrl');
            info($response->getBody());
        } elseif ($response->getStatusCode() == 200) {
            $message_body = json_decode($response->getBody(), true);

            $forwarding_url = $message_body['forward_url'];

            $account_settings = $account->account_email_settings;

            $account_settings->is_disabled = mb_strlen($forwarding_url) != 0;

            $account_settings->forward_url_for_v5 = rtrim($forwarding_url, '/');
            $account_settings->save();

            $billing_transferred = $message_body['billing_transferred'];

            if ($billing_transferred == 'true') {
                $company = $account->company;
                $company->plan = null;
                $company->plan_expires = null;
                $company->save();
            }
        } else {
            info('failed to auto forward');
            info(json_decode($response->getBody()->getContents()));
        }

        return back();
    }
}
