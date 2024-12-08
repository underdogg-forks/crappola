<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $is_deleted
 * @property string                          $name
 * @property int                             $public_id
 * @property \App\Models\Account             $account
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalCategory withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ProposalCategory extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @var string
     */
    //protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL_CATEGORY;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return '/proposals/categories/' . $this->public_id;
    }

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
