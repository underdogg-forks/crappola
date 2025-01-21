<?php

namespace App\Http\Requests;

use App\Models\Contact;

class UpdateDocumentRequest extends DocumentRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $contact = Contact::getContactIfLoggedIn();

        if ($contact && $contact->company->hasFeature(FEATURE_DOCUMENTS)) {
            return true;
        }
        if (! $this->entity()) {
            return false;
        }

        return (bool) $this->user()->can('edit', $this->entity());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
        ];
    }
}
