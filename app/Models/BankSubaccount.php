<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankSubaccount.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int                             $bank_account_id
 * @property string                          $account_name
 * @property string                          $account_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $public_id
 * @property \App\Models\BankAccount         $bank_account
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSubaccount withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BankSubaccount extends EntityModel
{
    use SoftDeletes;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_BANK_SUBACCOUNT;
    }

    public function bank_account()
    {
        return $this->belongsTo(\App\Models\BankAccount::class);
    }
}
