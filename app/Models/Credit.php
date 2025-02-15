<?php

namespace App\Models;

use App\Events\CreditWasCreated;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Credit.
 */
class Credit extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\CreditPresenter';

    protected $fillable = [
        'public_notes',
        'private_notes',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/credits/{$this->public_id}";
    }

    public function getEntityType()
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
            $this->balance = $this->balance - $amount;
        }

        $this->save();

        return $applied;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Credit::creating(function ($credit) {});

Credit::created(function ($credit) {
    event(new CreditWasCreated($credit));
});
