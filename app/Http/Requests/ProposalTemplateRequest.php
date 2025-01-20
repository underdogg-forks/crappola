<?php

namespace App\Http\Requests;

/**
 * Class ProposalTemplateRequest.
 */
class ProposalTemplateRequest extends EntityRequest
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_PROPOSAL_TEMPLATE;

    public function authorize(): bool
    {
        if ($this->user()->can('view', ENTITY_PROPOSAL)) {
            return true;
        }

        return (bool) $this->user()->can('createEntity', ENTITY_PROPOSAL);
    }
}
