<?php

namespace App\Http\Requests;

use App\Libraries\HistoryUtils;
use App\Models\Contact;
use App\Models\EntityModel;
use Input;
use App\Libraries\Utils;

class EntityRequest extends Request
{
    protected $entityType;

    private $entity;

    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

    public function authorize()
    {
        /*if ($this->entity()) {
            if ($this->user()->can('view', $this->entity())) {
                HistoryUtils::trackViewed($this->entity());

                return true;
            }
        } else {
            return $this->user()->can('createEntity', $this->entityType);
        }*/
        return true;
    }

    public function entity()
    {
        if ($this->entity) {
            return $this->entity;
        }

        $class = EntityModel::getClassName($this->entityType);

        // The entity id can appear as invoices, invoice_id, public_id or id
        $publicId = false;
        $field = $this->entityType . '_id';
        if (!empty($this->$field)) {
            $publicId = $this->$field;
        }
        if (!$publicId) {
            $field = Utils::pluralizeEntityType($this->entityType);
            if (!empty($this->$field)) {
                $publicId = $this->$field;
            }
        }
        if (!$publicId) {
            $field = $this->entityType;
            if (!empty($this->$field)) {
                $publicId = $this->$field;
            }
        }
        if (!$publicId) {
            $publicId = request()->get('public_id') ?: request()->get('id');
        }

        if (!$publicId) {
            dd("wait, what?");
            return;
        }

        //Support Client Portal Scopes
        $companyId = false;


        $cid = request()->get('client_id');
        $pid = request()->get('public_id');
        $ccid = request()->get('client');
        dd($cid, $pid, $ccid);



        if ($this->user()->company_id) {
            $companyId = $this->user()->company_id;
        } elseif (request()->get('company_id')) {
            $companyId = request()->get('company_id');
        } elseif ($contact = Contact::getContactIfLoggedIn()) {
            $companyId = $contact->company->id;
        }

        if (method_exists($class, 'trashed')) {
            $this->entity = $class::scope($publicId, $companyId)->withTrashed()->firstOrFail();
        } else {
            $this->entity = $class::scope($publicId, $companyId)->firstOrFail();
        }

        return $this->entity;
    }

    public function rules(): array
    {
        return [];
    }
}
