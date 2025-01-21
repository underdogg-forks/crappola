<?php

namespace App\Http\Requests;

use App\Models\Contact;

class DocumentRequest extends EntityRequest
{
    protected $entityType = ENTITY_DOCUMENT;

    public function authorize()
    {
        $contact = Contact::getContactIfLoggedIn();
        if (! $contact) {
            return $this->user()->can('view', $this->entity());
        }
        if (! $contact->company->hasFeature(FEATURE_DOCUMENTS)) {
            return $this->user()->can('view', $this->entity());
        }

        return true;
    }
}
