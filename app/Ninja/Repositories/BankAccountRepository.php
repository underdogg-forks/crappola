<?php

namespace App\Ninja\Repositories;

use App\Models\BankAccount;
use App\Models\BankSubaccount;

class BankAccountRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return \App\Models\BankAccount::class;
    }

    public function find($accountId)
    {
        return \Illuminate\Support\Facades\DB::table('bank_accounts')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.deleted_at', '=', null)
            ->where('bank_accounts.account_id', '=', $accountId)
            ->select(
                'bank_accounts.public_id',
                'banks.name as bank_name',
                'bank_accounts.deleted_at',
                'banks.bank_library_id'
            );
    }

    public function save(array $input)
    {
        $bankAccount = BankAccount::createNew();
        $bankAccount->username = \Illuminate\Support\Facades\Crypt::encrypt(trim($input['bank_username']));
        $bankAccount->fill($input);

        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $account->bank_accounts()->save($bankAccount);

        foreach ($input['bank_accounts'] as $data) {
            if ( ! isset($data['include'])) {
                continue;
            }
            if ( ! filter_var($data['include'], FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }
            $subaccount = BankSubaccount::createNew();
            $subaccount->account_name = trim($data['account_name']);
            $subaccount->account_number = trim($data['hashed_account_number']);
            $bankAccount->bank_subaccounts()->save($subaccount);
        }

        return $bankAccount;
    }
}
