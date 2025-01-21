<?php

namespace App\Http\Requests;

class SaveEmailSettings extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->is_admin && $this->user()->isPro();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{bcc_email: string, reply_to_email: string}
     */
    public function rules(): array
    {
        return [
            'bcc_email'      => 'email',
            'reply_to_email' => 'email',
        ];
    }
}
