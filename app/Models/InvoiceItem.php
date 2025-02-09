<?php

namespace App\Models;

use App\Ninja\Presenters\InvoiceItemPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class InvoiceItem.
 *
 * @property int          $id
 * @property int          $account_id
 * @property int          $user_id
 * @property int          $invoice_id
 * @property int|null     $product_id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property string       $product_key
 * @property string       $notes
 * @property string       $cost
 * @property string|null  $qty
 * @property string|null  $tax_name1
 * @property string|null  $tax_rate1
 * @property int          $public_id
 * @property string|null  $custom_value1
 * @property string|null  $custom_value2
 * @property string|null  $tax_name2
 * @property string       $tax_rate2
 * @property int          $invoice_item_type_id
 * @property string       $discount
 * @property Account|null $account
 * @property Invoice      $invoice
 * @property Product|null $product
 * @property User         $user
 *
 * @method static Builder|InvoiceItem newModelQuery()
 * @method static Builder|InvoiceItem newQuery()
 * @method static Builder|InvoiceItem onlyTrashed()
 * @method static Builder|InvoiceItem query()
 * @method static Builder|InvoiceItem scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|InvoiceItem whereAccountId($value)
 * @method static Builder|InvoiceItem whereCost($value)
 * @method static Builder|InvoiceItem whereCreatedAt($value)
 * @method static Builder|InvoiceItem whereCustomValue1($value)
 * @method static Builder|InvoiceItem whereCustomValue2($value)
 * @method static Builder|InvoiceItem whereDeletedAt($value)
 * @method static Builder|InvoiceItem whereDiscount($value)
 * @method static Builder|InvoiceItem whereId($value)
 * @method static Builder|InvoiceItem whereInvoiceId($value)
 * @method static Builder|InvoiceItem whereInvoiceItemTypeId($value)
 * @method static Builder|InvoiceItem whereNotes($value)
 * @method static Builder|InvoiceItem whereProductId($value)
 * @method static Builder|InvoiceItem whereProductKey($value)
 * @method static Builder|InvoiceItem wherePublicId($value)
 * @method static Builder|InvoiceItem whereQty($value)
 * @method static Builder|InvoiceItem whereTaxName1($value)
 * @method static Builder|InvoiceItem whereTaxName2($value)
 * @method static Builder|InvoiceItem whereTaxRate1($value)
 * @method static Builder|InvoiceItem whereTaxRate2($value)
 * @method static Builder|InvoiceItem whereUpdatedAt($value)
 * @method static Builder|InvoiceItem whereUserId($value)
 * @method static Builder|InvoiceItem withActiveOrSelected($id = false)
 * @method static Builder|InvoiceItem withArchived()
 * @method static Builder|InvoiceItem withTrashed()
 * @method static Builder|InvoiceItem withoutTrashed()
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
    protected $presenter = InvoiceItemPresenter::class;

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
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
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
