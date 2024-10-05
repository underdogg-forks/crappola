<?php

namespace App\Models;

use App\Events\CreditWasCreated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Credit.
 */
class Credit extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    /**
     * @return mixed
     */
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

    /**
     * @return mixed
     */
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
