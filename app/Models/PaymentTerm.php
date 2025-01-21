<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PaymentTerm.
 *
 * @property int         $id
 * @property int         $num_days
 * @property string      $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int         $user_id
 * @property int         $account_id
 * @property int         $public_id
 *
 * @method static Builder|PaymentTerm newModelQuery()
 * @method static Builder|PaymentTerm newQuery()
 * @method static Builder|PaymentTerm onlyTrashed()
 * @method static Builder|PaymentTerm query()
 * @method static Builder|PaymentTerm scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|PaymentTerm whereAccountId($value)
 * @method static Builder|PaymentTerm whereCreatedAt($value)
 * @method static Builder|PaymentTerm whereDeletedAt($value)
 * @method static Builder|PaymentTerm whereId($value)
 * @method static Builder|PaymentTerm whereName($value)
 * @method static Builder|PaymentTerm whereNumDays($value)
 * @method static Builder|PaymentTerm wherePublicId($value)
 * @method static Builder|PaymentTerm whereUpdatedAt($value)
 * @method static Builder|PaymentTerm whereUserId($value)
 * @method static Builder|PaymentTerm withActiveOrSelected($id = false)
 * @method static Builder|PaymentTerm withArchived()
 * @method static Builder|PaymentTerm withTrashed()
 * @method static Builder|PaymentTerm withoutTrashed()
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
