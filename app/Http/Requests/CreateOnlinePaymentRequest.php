<?php

namespace App\Http\Requests;

use App\Models\GatewayType;
use App\Models\Invitation;

class CreateOnlinePaymentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $account = $this->invitation->account;

        $paymentDriver = $account->paymentDriver($this->invitation, $this->gateway_type);

        return $paymentDriver->rules();
    }

    public function sanitize()
    {
        $input = $this->all();

        $invitation = Invitation::with('invoice.invoice_items', 'invoice.client.currency', 'invoice.client.account.currency', 'invoice.client.account.account_gateways.gateway')
            ->where('invitation_key', '=', $this->invitation_key)
            ->firstOrFail();

        $input['invitation'] = $invitation;

        if ($gatewayTypeAlias = request()->gateway_type) {
            if ($gatewayTypeAlias != GATEWAY_TYPE_TOKEN) {
                $input['gateway_type'] = GatewayType::getIdFromAlias($gatewayTypeAlias);
            } else {
                $input['gateway_type'] = $gatewayTypeAlias;
            }
        } else {
            $input['gateway_type'] = session($invitation->id . 'gateway_type');
        }

        $this->replace($input);

        return $this->all();
    }
}
