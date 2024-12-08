<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 *
 * @property int                             $id
 * @property int                             $num_days
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $user_id
 * @property int                             $account_id
 * @property int                             $public_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereNumDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm withoutTrashed()
 *
 * @mixin \Eloquent
 */
class PaymentTerm extends EntityModel
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    protected $casts = ['deleted_at' => 'datetime'];

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

    public function getEntityType(): string
    {
        return ENTITY_PAYMENT_TERM;
    }

    public function getNumDays()
    {
        return $this->num_days == -1 ? 0 : $this->num_days;
    }
}
