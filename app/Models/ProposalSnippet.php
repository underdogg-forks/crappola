<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class ProposalSnippet extends EntityModel
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
        'icon',
        'private_notes',
        'proposal_category_id',
        'html',
        'css',
    ];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ProposalSnippetPresenter::class;

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL_SNIPPET;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return '/proposals/snippets/' . $this->public_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal_category()
    {
        return $this->belongsTo(\App\Models\ProposalCategory::class)->withTrashed();
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
