<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class InvoiceItem.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int                             $invoice_id
 * @property int|null                        $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string                          $product_key
 * @property string                          $notes
 * @property string                          $cost
 * @property string|null                     $qty
 * @property string|null                     $tax_name1
 * @property string|null                     $tax_rate1
 * @property int                             $public_id
 * @property string|null                     $custom_value1
 * @property string|null                     $custom_value2
 * @property string|null                     $tax_name2
 * @property string                          $tax_rate2
 * @property int                             $invoice_item_type_id
 * @property string                          $discount
 * @property \App\Models\Account|null        $account
 * @property \App\Models\Invoice             $invoice
 * @property \App\Models\Product|null        $product
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceItemTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereProductKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InvoiceItem extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\InvoiceItemPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'invoice_item_type_id',
        'discount',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_INVOICE_ITEM;
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function getPreTaxAmount(): int|float
    {
        $amount = $this->cost * $this->qty;

        if ($this->discount != 0) {
            if ($this->invoice->is_amount_discount) {
                $amount -= $this->discount;
            } else {
                $amount -= round($amount * $this->discount / 100, 4);
            }
        }

        return $amount;
    }

    public function getTaxAmount(): float|int
    {
        $tax = 0;
        $preTaxAmount = $this->getPreTaxAmount();

        if ($this->tax_rate1) {
            $tax += round($preTaxAmount * $this->tax_rate1 / 100, 2);
        }

        if ($this->tax_rate2) {
            $tax += round($preTaxAmount * $this->tax_rate2 / 100, 2);
        }

        return $tax;
    }

    public function amount(): float|int|array
    {
        return $this->getPreTaxAmount() + $this->getTaxAmount();
    }

    public function markFeePaid(): void
    {
        if ($this->invoice_item_type_id == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
            $this->invoice_item_type_id = INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE;
            $this->save();
        }
    }

    public function hasTaxes(): bool
    {
        if ($this->tax_name1 || $this->tax_rate1) {
            return true;
        }

        if ($this->tax_name2 || $this->tax_rate2) {
            return false;
        }

        return false;
    }

    public function costWithDiscount()
    {
        $cost = $this->cost;

        if ($this->discount != 0) {
            if ($this->invoice->is_amount_discount) {
                $cost -= $this->discount / $this->qty;
            } else {
                $cost -= $cost * $this->discount / 100;
            }
        }

        return $cost;
    }
}
