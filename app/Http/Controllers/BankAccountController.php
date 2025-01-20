<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAccountRequest;
use App\Libraries\Utils;
use App\Models\BankAccount;
use App\Ninja\Repositories\BankAccountRepository;
use App\Services\BankAccountService;
use Cache;
use Crypt;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

class BankAccountController extends BaseController
{
    protected BankAccountService $bankAccountService;

    protected BankAccountRepository $bankAccountRepo;

    public function __construct(BankAccountService $bankAccountService, BankAccountRepository $bankAccountRepo)
    {
        //parent::__construct();

        $this->bankAccountService = $bankAccountService;
        $this->bankAccountRepo = $bankAccountRepo;
    }

    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_BANKS);
    }

    public function getDatatable()
    {
        return $this->bankAccountService->getDatatable(Auth::user()->company_id);
    }

    public function edit($publicId)
    {
        $bankAccount = BankAccount::scope($publicId)->firstOrFail();

        $data = [
            'title'       => trans('texts.edit_bank_account'),
            'banks'       => Cache::get('banks'),
            'bankAccount' => $bankAccount,
        ];

        return View::make('companies.bank_account', $data);
    }

    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * Displays the form for company creation.
     */
    public function create()
    {
        $data = [
            'banks'       => Cache::get('banks'),
            'bankAccount' => null,
        ];

        return View::make('companies.bank_account', $data);
    }

    public function bulk()
    {
        $action = $request->get('bulk_action');
        $ids = $request->get('bulk_public_id');
        $count = $this->bankAccountService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_bank_account'));

        return Redirect::to('settings/' . ACCOUNT_BANKS);
    }

    public function validateAccount()
    {
        $publicId = $request->get('public_id');
        $username = trim($request->get('bank_username'));
        $password = trim($request->get('bank_password'));

        if ($publicId) {
            $bankAccount = BankAccount::scope($publicId)->firstOrFail();
            if ($username != $bankAccount->username) {
                $bankAccount->setUsername($username);
                $bankAccount->save();
            } else {
                $username = Crypt::decrypt($username);
            }
            $bankId = $bankAccount->bank_id;
        } else {
            $bankAccount = new BankAccount();
            $bankAccount->bank_id = $request->get('bank_id');
        }

        $bankAccount->app_version = $request->get('app_version');
        $bankAccount->ofx_version = $request->get('ofx_version');

        if ($publicId) {
            $bankAccount->save();
        }

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, $publicId));
    }

    public function store(CreateBankAccountRequest $request)
    {
        $bankAccount = $this->bankAccountRepo->save(Input::all());

        $bankId = $request->get('bank_id');
        $username = trim($request->get('bank_username'));
        $password = trim($request->get('bank_password'));

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, true));
    }

    public function importExpenses($bankId)
    {
        return $this->bankAccountService->importExpenses($bankId, Input::all());
    }

    public function showImportOFX()
    {
        return view('companies.import_ofx');
    }

    public function doImportOFX(Request $request)
    {
        $file = File::get($request->file('ofx_file'));

        try {
            $data = $this->bankAccountService->parseOFX($file);
        } catch (Exception $e) {
            Session::now('error', trans('texts.ofx_parse_failed'));
            Utils::logError($e);

            return view('companies.import_ofx');
        }

        $data = [
            'banks'        => null,
            'bankAccount'  => null,
            'transactions' => json_encode([$data]),
        ];

        return View::make('companies.bank_account', $data);
    }
}
