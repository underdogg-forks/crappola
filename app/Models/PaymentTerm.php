<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 */
class PaymentTerm extends EntityModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    public static function getSelectOptions()
    {
        $terms = self::whereAccountId(0)->get();

        foreach (self::scope()->get() as $term) {
            $terms->push($term);
        }

        foreach ($terms as $term) {
            $term->name = trans('texts.payment_terms_net') . ' ' . $term->getNumDays();
        }

        return $terms->sortBy('num_days');
    }

    public function getEntityType()
    {
        return ENTITY_PAYMENT_TERM;
    }

    public function getNumDays()
    {
        return $this->num_days == -1 ? 0 : $this->num_days;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
