<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAccountRequest;
use App\Models\Account;
use App\Models\BankAccount;
use App\Ninja\Repositories\BankAccountRepository;
use App\Services\BankAccountService;
use Exception;
use Illuminate\Http\Request;
use Utils;

class BankAccountController extends BaseController
{
    protected \App\Services\BankAccountService $bankAccountService;

    protected \App\Ninja\Repositories\BankAccountRepository $bankAccountRepo;

    public function __construct(BankAccountService $bankAccountService, BankAccountRepository $bankAccountRepo)
    {
        //parent::__construct();

        $this->bankAccountService = $bankAccountService;
        $this->bankAccountRepo = $bankAccountRepo;
    }

    public function index()
    {
        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_BANKS);
    }

    public function getDatatable()
    {
        return $this->bankAccountService->getDatatable(\Illuminate\Support\Facades\Auth::user()->account_id);
    }

    public function edit($publicId)
    {
        $bankAccount = BankAccount::scope($publicId)->firstOrFail();

        $data = [
            'title'       => trans('texts.edit_bank_account'),
            'banks'       => \Illuminate\Support\Facades\Cache::get('banks'),
            'bankAccount' => $bankAccount,
        ];

        return \Illuminate\Support\Facades\View::make('accounts.bank_account', $data);
    }

    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * Displays the form for account creation.
     */
    public function create()
    {
        $data = [
            'banks'       => \Illuminate\Support\Facades\Cache::get('banks'),
            'bankAccount' => null,
        ];

        return \Illuminate\Support\Facades\View::make('accounts.bank_account', $data);
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('bulk_action');
        $ids = \Illuminate\Support\Facades\Request::input('bulk_public_id');
        $count = $this->bankAccountService->bulk($ids, $action);

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.archived_bank_account'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_BANKS);
    }

    public function validateAccount()
    {
        $publicId = \Illuminate\Support\Facades\Request::input('public_id');
        $username = trim(\Illuminate\Support\Facades\Request::input('bank_username'));
        $password = trim(\Illuminate\Support\Facades\Request::input('bank_password'));

        if ($publicId) {
            $bankAccount = BankAccount::scope($publicId)->firstOrFail();
            if ($username != $bankAccount->username) {
                $bankAccount->setUsername($username);
                $bankAccount->save();
            } else {
                $username = \Illuminate\Support\Facades\Crypt::decrypt($username);
            }

            $bankId = $bankAccount->bank_id;
        } else {
            $bankAccount = new BankAccount();
            $bankAccount->bank_id = \Illuminate\Support\Facades\Request::input('bank_id');
        }

        $bankAccount->app_version = \Illuminate\Support\Facades\Request::input('app_version');
        $bankAccount->ofx_version = \Illuminate\Support\Facades\Request::input('ofx_version');

        if ($publicId) {
            $bankAccount->save();
        }

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, $publicId));
    }

    public function store(CreateBankAccountRequest $request)
    {
        $bankAccount = $this->bankAccountRepo->save(Request::all());

        $bankId = \Illuminate\Support\Facades\Request::input('bank_id');
        $username = trim(\Illuminate\Support\Facades\Request::input('bank_username'));
        $password = trim(\Illuminate\Support\Facades\Request::input('bank_password'));

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, true));
    }

    public function importExpenses($bankId)
    {
        return $this->bankAccountService->importExpenses($bankId, Request::all());
    }

    public function showImportOFX()
    {
        return view('accounts.import_ofx');
    }

    public function doImportOFX(Request $request)
    {
        $file = \Illuminate\Support\Facades\File::get($request->file('ofx_file'));

        try {
            $data = $this->bankAccountService->parseOFX($file);
        } catch (Exception $exception) {
            \Illuminate\Support\Facades\Session::now('error', trans('texts.ofx_parse_failed'));
            Utils::logError($exception);

            return view('accounts.import_ofx');
        }

        $data = [
            'banks'        => null,
            'bankAccount'  => null,
            'transactions' => json_encode([$data]),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.bank_account', $data);
    }
}
