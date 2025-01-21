<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * Class BankAccount.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $bank_id
 * @property int                             $user_id
 * @property string                          $username
 * @property Carbon|null                     $created_at
 * @property Carbon|null                     $updated_at
 * @property Carbon|null                     $deleted_at
 * @property int                             $public_id
 * @property int                             $app_version
 * @property int                             $ofx_version
 * @property Bank                            $bank
 * @property Collection<int, BankSubaccount> $bank_subaccounts
 * @property int|null                        $bank_subaccounts_count
 *
 * @method static Builder|BankAccount newModelQuery()
 * @method static Builder|BankAccount newQuery()
 * @method static Builder|BankAccount onlyTrashed()
 * @method static Builder|BankAccount query()
 * @method static Builder|BankAccount scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|BankAccount whereAccountId($value)
 * @method static Builder|BankAccount whereAppVersion($value)
 * @method static Builder|BankAccount whereBankId($value)
 * @method static Builder|BankAccount whereCreatedAt($value)
 * @method static Builder|BankAccount whereDeletedAt($value)
 * @method static Builder|BankAccount whereId($value)
 * @method static Builder|BankAccount whereOfxVersion($value)
 * @method static Builder|BankAccount wherePublicId($value)
 * @method static Builder|BankAccount whereUpdatedAt($value)
 * @method static Builder|BankAccount whereUserId($value)
 * @method static Builder|BankAccount whereUsername($value)
 * @method static Builder|BankAccount withActiveOrSelected($id = false)
 * @method static Builder|BankAccount withArchived()
 * @method static Builder|BankAccount withTrashed()
 * @method static Builder|BankAccount withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BankAccount extends EntityModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'app_version',
        'ofx_version',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_BANK_ACCOUNT;
    }

    public function getUsername()
    {
        return Crypt::decrypt($this->username);
    }

    /**
     * @param $config
     */
    public function setUsername($value): void
    {
        $this->username = Crypt::encrypt($value);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function bank_subaccounts()
    {
        return $this->hasMany(BankSubaccount::class);
    }
}
