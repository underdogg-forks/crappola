<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                             $id
 * @property int|null                        $account_id
 * @property int|null                        $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $is_deleted
 * @property string                          $private_notes
 * @property string                          $name
 * @property string                          $html
 * @property string                          $css
 * @property int                             $public_id
 * @property \App\Models\Account|null        $account
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalTemplate withoutTrashed()
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
    protected $presenter = \App\Ninja\Presenters\ProposalTemplatePresenter::class;

    protected $casts = ['deleted_at' => 'datetime'];

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
