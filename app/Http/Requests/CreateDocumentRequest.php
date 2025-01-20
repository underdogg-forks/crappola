<?php

namespace App\Http\Requests;

class CreateDocumentRequest extends DocumentRequest
{
    protected $autoload = [
        ENTITY_INVOICE,
        ENTITY_EXPENSE,
    ];

    public function authorize(): bool
    {
        return $this->user()->hasFeature(FEATURE_DOCUMENTS);
    }

    public function rules(): array
    {
        return [
            //'file' => 'mimes:jpg'
        ];
    }
}
