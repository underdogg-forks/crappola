<?php

namespace App\Models;

/**
 * Class InvoiceStatus.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceStatus whereName($value)
 *
 * @mixin \Eloquent
 */
class InvoiceStatus extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public static function getIdFromAlias($status): int|false
    {
        return match ($status) {
            'draft'    => INVOICE_STATUS_DRAFT,
            'sent'     => INVOICE_STATUS_SENT,
            'viewed'   => INVOICE_STATUS_VIEWED,
            'approved' => INVOICE_STATUS_APPROVED,
            'partial'  => INVOICE_STATUS_PARTIAL,
            'overdue'  => INVOICE_STATUS_OVERDUE,
            'unpaid'   => INVOICE_STATUS_UNPAID,
            default    => false,
        };
    }

    public function getTranslatedName()
    {
        return trans('texts.status_' . \Illuminate\Support\Str::slug($this->name, '_'));
    }
}
