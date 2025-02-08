<?php

namespace App\Models;

use Cache;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 */
class PaymentTerm extends EntityModel
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    public static function getSelectOptions()
    {
        $terms = self::whereCompanyPlanId(0)->get();

        if (!isset($terms) || count($terms) === 0) {
            return null;
        }

        foreach (self::scope()->get() as $term) {
            $terms->push($term);
        }

        foreach ($terms as $term) {
            $term->name = trans('texts.payment_terms_net') . ' ' . $term->getNumDays();
        }

        return $terms->sortBy('num_days');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
