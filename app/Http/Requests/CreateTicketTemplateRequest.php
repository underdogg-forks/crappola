<?php

namespace App\Http\Requests;

use App\Models\Ticket;

class CreateTicketTemplateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Ticket::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string, description: string}
     */
    public function rules(): array
    {
        return [
            'name'        => 'required',
            'description' => 'required',
        ];
    }
}
