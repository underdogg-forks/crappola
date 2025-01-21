<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 */
class PaymentTerm extends EntityModel
{
    use SoftDeletes;

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public static function getSelectOptions()
    {
        $terms = self::whereCompanyPlanId(0)->get();
        if (! isset($terms)) {
            return;
        }
        if (count($terms) === 0) {
            return;
        }

        foreach (self::scope()->get() as $term) {
            $terms->push($term);
        }

        foreach ($terms as $term) {
            $term->name = trans('texts.payment_terms_net') . ' ' . $term->getNumDays();
        }

        return $terms->sortBy('num_days');
    }

    public function getNumDays()
    {
        return $this->num_days == -1 ? 0 : $this->num_days;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PAYMENT_TERM;
    }
}
