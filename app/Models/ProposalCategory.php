<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int         $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int         $is_deleted
 * @property string      $name
 * @property int         $public_id
 * @property Account     $account
 *
 * @method static Builder|ProposalCategory newModelQuery()
 * @method static Builder|ProposalCategory newQuery()
 * @method static Builder|ProposalCategory onlyTrashed()
 * @method static Builder|ProposalCategory query()
 * @method static Builder|ProposalCategory scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|ProposalCategory whereAccountId($value)
 * @method static Builder|ProposalCategory whereCreatedAt($value)
 * @method static Builder|ProposalCategory whereDeletedAt($value)
 * @method static Builder|ProposalCategory whereId($value)
 * @method static Builder|ProposalCategory whereIsDeleted($value)
 * @method static Builder|ProposalCategory whereName($value)
 * @method static Builder|ProposalCategory wherePublicId($value)
 * @method static Builder|ProposalCategory whereUpdatedAt($value)
 * @method static Builder|ProposalCategory whereUserId($value)
 * @method static Builder|ProposalCategory withActiveOrSelected($id = false)
 * @method static Builder|ProposalCategory withArchived()
 * @method static Builder|ProposalCategory withTrashed()
 * @method static Builder|ProposalCategory withoutTrashed()
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

    public function getRoute(): string
    {
        return '/proposals/categories/' . $this->public_id;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
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
