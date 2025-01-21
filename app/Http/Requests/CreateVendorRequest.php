<?php

namespace App\Http\Requests;

use App\Models\Vendor;

class CreateVendorRequest extends VendorRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Vendor::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string}
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
