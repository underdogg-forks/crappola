<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class InvoiceItem.
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
    protected $dates = ['deleted_at'];

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

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_INVOICE_ITEM;
    }

    /**
     * @return BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function amount()
    {
        return $this->getPreTaxAmount() + $this->getTaxAmount();
    }

    public function getPreTaxAmount()
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

    public function getTaxAmount()
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

    public function markFeePaid(): void
    {
        if ($this->invoice_item_type_id == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
            $this->invoice_item_type_id = INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE;
            $this->save();
        }
    }

    public function hasTaxes(): bool
    {
        if ($this->tax_name1) {
            return true;
        }
        if ($this->tax_rate1) {
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
