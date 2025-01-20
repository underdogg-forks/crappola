<?php

namespace App\Models\Traits;

/**
 * Class ChargesFees.
 */
trait ChargesFees
{
    public function calcGatewayFee($gatewayTypeId = false, $includeTax = false, $gatewayFeeItem = 0): bool|float
    {
        $company = $this->company;
        $settings = $company->getGatewaySettings($gatewayTypeId);
        $fee = 0;
        $fee_cap = $settings->fee_cap;

        if (! $company->gateway_fee_enabled) {
            return false;
        }

        if ($settings->fee_amount) {
            $fee += $settings->fee_amount;
        }

        if ($settings->fee_percent) {
            $amount = $this->partial > 0 ? $this->partial : $this->balance;

            //If gateway fee has already been selected exclude the fee on the amount.
            $amount = $gatewayFeeItem > 0 ? $amount - $gatewayFeeItem : $amount + abs($gatewayFeeItem);

            if ($settings->adjust_fee_percent) {
                $fee += ($amount + $fee) / (1 - $settings->fee_percent / 100) - ($amount + $fee);
            } else {
                $fee += $amount * $settings->fee_percent / 100;
            }
        }

        // calculate final amount with tax
        if ($includeTax) {
            $preTaxFee = $fee;

            if ($settings->fee_tax_rate1) {
                $fee += $preTaxFee * $settings->fee_tax_rate1 / 100;
            }

            if ($settings->fee_tax_rate2) {
                $fee += $preTaxFee * $settings->fee_tax_rate2 / 100;
            }
        }

        if ($fee_cap != 0) {
            $fee = min($fee, $fee_cap);
        }

        return round($fee, 2);
    }

    public function getGatewayFee()
    {
        $company = $this->company;

        if (! $company->gateway_fee_enabled) {
            return 0;
        }

        $item = $this->getGatewayFeeItem();

        return $item ? $item->amount() : 0;
    }

    public function getGatewayFeeItem()
    {
        if (! $this->relationLoaded('invoice_items')) {
            $this->load('invoice_items');
        }

        foreach ($this->invoice_items as $item) {
            if ($item->invoice_item_type_id == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
                return $item;
            }
        }

        return false;
    }
}
