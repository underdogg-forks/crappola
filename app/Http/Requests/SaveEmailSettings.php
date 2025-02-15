<?php

namespace App\Http\Requests;

class SaveEmailSettings extends Request
{
    public function authorize()
    {
        return $this->user()->is_admin && $this->user()->isPro();
    }

    public function rules()
    {
        return [
            'bcc_email'      => 'email',
            'reply_to_email' => 'email',
        ];
    }
}
