<?php

namespace App\Http\Requests;

class UpdateDocumentRequest extends DocumentRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
        ];
    }
}
