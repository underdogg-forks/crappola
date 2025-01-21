<?php

namespace App\Models;

use App\Ninja\Presenters\ProposalTemplatePresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int          $id
 * @property int|null     $account_id
 * @property int|null     $user_id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property int          $is_deleted
 * @property string       $private_notes
 * @property string       $name
 * @property string       $html
 * @property string       $css
 * @property int          $public_id
 * @property Account|null $account
 *
 * @method static Builder|ProposalTemplate newModelQuery()
 * @method static Builder|ProposalTemplate newQuery()
 * @method static Builder|ProposalTemplate onlyTrashed()
 * @method static Builder|ProposalTemplate query()
 * @method static Builder|ProposalTemplate scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|ProposalTemplate whereAccountId($value)
 * @method static Builder|ProposalTemplate whereCreatedAt($value)
 * @method static Builder|ProposalTemplate whereCss($value)
 * @method static Builder|ProposalTemplate whereDeletedAt($value)
 * @method static Builder|ProposalTemplate whereHtml($value)
 * @method static Builder|ProposalTemplate whereId($value)
 * @method static Builder|ProposalTemplate whereIsDeleted($value)
 * @method static Builder|ProposalTemplate whereName($value)
 * @method static Builder|ProposalTemplate wherePrivateNotes($value)
 * @method static Builder|ProposalTemplate wherePublicId($value)
 * @method static Builder|ProposalTemplate whereUpdatedAt($value)
 * @method static Builder|ProposalTemplate whereUserId($value)
 * @method static Builder|ProposalTemplate withActiveOrSelected($id = false)
 * @method static Builder|ProposalTemplate withArchived()
 * @method static Builder|ProposalTemplate withTrashed()
 * @method static Builder|ProposalTemplate withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ProposalTemplate extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

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
    protected $presenter = ProposalTemplatePresenter::class;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL_TEMPLATE;
    }

    public function getRoute(): string
    {
        return '/proposals/templates/' . $this->public_id;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
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
