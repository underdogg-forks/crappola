<?php

namespace App\Http\Requests;

class CreateDocumentRequest extends DocumentRequest
{
    protected $autoload = [
        ENTITY_INVOICE,
        ENTITY_EXPENSE,
        ENTITY_TICKET,
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (session('contact_key')) {
            return true;
        }

        if (! $this->user()->hasFeature(FEATURE_DOCUMENTS)) {
            return false;
        }

        if ($this->invoice && $this->user()->cannot('edit', $this->invoice)) {
            return false;
        }

        if ($this->expense && $this->user()->cannot('edit', $this->expense)) {
            return false;
        }

        if (! $this->ticket) {
            return true;
        }

        if (! $this->user()->cannot('edit', $this->ticket)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            //'file' => 'mimes:jpg'
        ];
    }
}
