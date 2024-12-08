<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class BankSubaccount.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int         $user_id
 * @property int         $bank_account_id
 * @property string      $account_name
 * @property string      $account_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int         $public_id
 * @property BankAccount $bank_account
 *
 * @method static Builder|BankSubaccount newModelQuery()
 * @method static Builder|BankSubaccount newQuery()
 * @method static Builder|BankSubaccount onlyTrashed()
 * @method static Builder|BankSubaccount query()
 * @method static Builder|BankSubaccount scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|BankSubaccount whereAccountId($value)
 * @method static Builder|BankSubaccount whereAccountName($value)
 * @method static Builder|BankSubaccount whereAccountNumber($value)
 * @method static Builder|BankSubaccount whereBankAccountId($value)
 * @method static Builder|BankSubaccount whereCreatedAt($value)
 * @method static Builder|BankSubaccount whereDeletedAt($value)
 * @method static Builder|BankSubaccount whereId($value)
 * @method static Builder|BankSubaccount wherePublicId($value)
 * @method static Builder|BankSubaccount whereUpdatedAt($value)
 * @method static Builder|BankSubaccount whereUserId($value)
 * @method static Builder|BankSubaccount withActiveOrSelected($id = false)
 * @method static Builder|BankSubaccount withArchived()
 * @method static Builder|BankSubaccount withTrashed()
 * @method static Builder|BankSubaccount withoutTrashed()
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
        return $this->belongsTo(BankAccount::class);
    }
}
