<?php

namespace App\Http\Requests;

class UpdateDocumentRequest extends DocumentRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [
        ];
    }
}
