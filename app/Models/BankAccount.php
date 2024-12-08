<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount.
 *
 * @property int                                                                       $id
 * @property int                                                                       $account_id
 * @property int                                                                       $bank_id
 * @property int                                                                       $user_id
 * @property string                                                                    $username
 * @property \Illuminate\Support\Carbon|null                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                           $updated_at
 * @property \Illuminate\Support\Carbon|null                                           $deleted_at
 * @property int                                                                       $public_id
 * @property int                                                                       $app_version
 * @property int                                                                       $ofx_version
 * @property \App\Models\Bank                                                          $bank
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankSubaccount> $bank_subaccounts
 * @property int|null                                                                  $bank_subaccounts_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereOfxVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount withoutTrashed()
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
        return \Illuminate\Support\Facades\Crypt::decrypt($this->username);
    }

    /**
     * @param $config
     */
    public function setUsername($value): void
    {
        $this->username = \Illuminate\Support\Facades\Crypt::encrypt($value);
    }

    public function bank()
    {
        return $this->belongsTo(\App\Models\Bank::class);
    }

    public function bank_subaccounts()
    {
        return $this->hasMany(\App\Models\BankSubaccount::class);
    }
}
