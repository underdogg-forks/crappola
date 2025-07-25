<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class ProposalSnippet extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

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
    protected $presenter = 'App\Ninja\Presenters\ProposalSnippetPresenter';

    public function getEntityType()
    {
        return ENTITY_PROPOSAL_SNIPPET;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/proposals/snippets/{$this->public_id}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal_category()
    {
        return $this->belongsTo('App\Models\ProposalCategory')->withTrashed();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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
