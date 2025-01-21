<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class InvoiceStatus.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static Builder|InvoiceStatus newModelQuery()
 * @method static Builder|InvoiceStatus newQuery()
 * @method static Builder|InvoiceStatus query()
 * @method static Builder|InvoiceStatus whereId($value)
 * @method static Builder|InvoiceStatus whereName($value)
 *
 * @mixin \Eloquent
 */
class InvoiceStatus extends Model
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
        return trans('texts.status_' . Str::slug($this->name, '_'));
    }
}
