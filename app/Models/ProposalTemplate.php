<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class ProposalTemplate extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'private_notes',
        'html',
        'css',
    ];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ProposalTemplatePresenter::class;

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL_TEMPLATE;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return '/proposals/templates/' . $this->public_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}

/*
Proposal::creating(function ($project) {
    $project->setNullValues();
});

Proposal::updating(function ($project) {
    $project->setNullValues();
});
*/
