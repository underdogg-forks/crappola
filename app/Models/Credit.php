<?php

namespace App\Models;

use App\Events\CreditWasCreated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Credit.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $client_id
 * @property int                             $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $is_deleted
 * @property string                          $amount
 * @property string                          $balance
 * @property string|null                     $credit_date
 * @property string|null                     $credit_number
 * @property string                          $private_notes
 * @property int                             $public_id
 * @property string|null                     $public_notes
 * @property \App\Models\Account             $account
 * @property \App\Models\Client              $client
 * @property \App\Models\Invoice|null        $invoice
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Credit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreditDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreditNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Credit extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\CreditPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'public_notes',
        'private_notes',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return '/credits/' . $this->public_id;
    }

    public function getEntityType(): string
    {
        return ENTITY_CREDIT;
    }

    /**
     * @param $amount
     *
     * @return mixed
     */
    public function apply($amount)
    {
        if ($amount > $this->balance) {
            $applied = $this->balance;
            $this->balance = 0;
        } else {
            $applied = $amount;
            $this->balance -= $amount;
        }

        $this->save();

        return $applied;
    }
}

Credit::creating(function ($credit): void {});

Credit::created(function ($credit): void {
    event(new CreditWasCreated($credit));
});
