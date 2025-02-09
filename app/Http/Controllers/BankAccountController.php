<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAccountRequest;
use App\Models\Account;
use App\Models\BankAccount;
use App\Ninja\Repositories\BankAccountRepository;
use App\Services\BankAccountService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Utils;

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
        return $this->bankAccountService->getDatatable(Auth::user()->account_id);
    }

    public function edit($publicId)
    {
        $bankAccount = BankAccount::scope($publicId)->firstOrFail();

        $data = [
            'title'       => trans('texts.edit_bank_account'),
            'banks'       => Cache::get('banks'),
            'bankAccount' => $bankAccount,
        ];

        return View::make('accounts.bank_account', $data);
    }

    public function update($publicId)
    {
        return BankAccount::save($publicId);
    }

    /**
     * Displays the form for account creation.
     */
    public function create()
    {
        $data = [
            'banks'       => Cache::get('banks'),
            'bankAccount' => null,
        ];

        return View::make('accounts.bank_account', $data);
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('bulk_action');
        $ids = \Illuminate\Support\Facades\Request::input('bulk_public_id');
        $count = $this->bankAccountService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_bank_account'));

        return Redirect::to('settings/' . ACCOUNT_BANKS);
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
                $username = Crypt::decrypt($username);
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
        $bankAccount = $this->bankAccountRepo->save($request->all());

        $bankId = \Illuminate\Support\Facades\Request::input('bank_id');
        $username = trim(\Illuminate\Support\Facades\Request::input('bank_username'));
        $password = trim(\Illuminate\Support\Facades\Request::input('bank_password'));

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, true));
    }

    public function importExpenses($bankId)
    {
        return $this->bankAccountService->importExpenses($bankId, request()->all());
    }

    public function showImportOFX()
    {
        return view('accounts.import_ofx');
    }

    public function doImportOFX(Request $request)
    {
        $file = File::get($request->file('ofx_file'));

        try {
            $data = $this->bankAccountService->parseOFX($file);
        } catch (Exception $exception) {
            Session::now('error', trans('texts.ofx_parse_failed'));
            Utils::logError($exception);

            return view('accounts.import_ofx');
        }

        $data = [
            'banks'        => null,
            'bankAccount'  => null,
            'transactions' => json_encode([$data]),
        ];

        return View::make('accounts.bank_account', $data);
    }
}
