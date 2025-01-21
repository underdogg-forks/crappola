<?php

namespace App\Models;

use App\Events\CreditWasCreated;
use App\Ninja\Presenters\CreditPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Credit.
 *
 * @property int          $id
 * @property int          $account_id
 * @property int          $client_id
 * @property int          $user_id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property int          $is_deleted
 * @property string       $amount
 * @property string       $balance
 * @property string|null  $credit_date
 * @property string|null  $credit_number
 * @property string       $private_notes
 * @property int          $public_id
 * @property string|null  $public_notes
 * @property Account      $account
 * @property Client       $client
 * @property Invoice|null $invoice
 * @property User         $user
 *
 * @method static Builder|Credit newModelQuery()
 * @method static Builder|Credit newQuery()
 * @method static Builder|Credit onlyTrashed()
 * @method static Builder|Credit query()
 * @method static Builder|Credit scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Credit whereAccountId($value)
 * @method static Builder|Credit whereAmount($value)
 * @method static Builder|Credit whereBalance($value)
 * @method static Builder|Credit whereClientId($value)
 * @method static Builder|Credit whereCreatedAt($value)
 * @method static Builder|Credit whereCreditDate($value)
 * @method static Builder|Credit whereCreditNumber($value)
 * @method static Builder|Credit whereDeletedAt($value)
 * @method static Builder|Credit whereId($value)
 * @method static Builder|Credit whereIsDeleted($value)
 * @method static Builder|Credit wherePrivateNotes($value)
 * @method static Builder|Credit wherePublicId($value)
 * @method static Builder|Credit wherePublicNotes($value)
 * @method static Builder|Credit whereUpdatedAt($value)
 * @method static Builder|Credit whereUserId($value)
 * @method static Builder|Credit withActiveOrSelected($id = false)
 * @method static Builder|Credit withArchived()
 * @method static Builder|Credit withTrashed()
 * @method static Builder|Credit withoutTrashed()
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
    protected $presenter = CreditPresenter::class;

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
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function getName(): string
    {
        return '';
    }

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
