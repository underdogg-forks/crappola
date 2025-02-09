<?php

namespace App\Models;

use App\Ninja\Presenters\ProposalSnippetPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                   $id
 * @property int                   $account_id
 * @property int                   $user_id
 * @property Carbon|null           $created_at
 * @property Carbon|null           $updated_at
 * @property Carbon|null           $deleted_at
 * @property int                   $is_deleted
 * @property int|null              $proposal_category_id
 * @property string                $name
 * @property string                $icon
 * @property string                $private_notes
 * @property string                $html
 * @property string                $css
 * @property int                   $public_id
 * @property Account               $account
 * @property ProposalCategory|null $proposal_category
 *
 * @method static Builder|ProposalSnippet newModelQuery()
 * @method static Builder|ProposalSnippet newQuery()
 * @method static Builder|ProposalSnippet onlyTrashed()
 * @method static Builder|ProposalSnippet query()
 * @method static Builder|ProposalSnippet scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|ProposalSnippet whereAccountId($value)
 * @method static Builder|ProposalSnippet whereCreatedAt($value)
 * @method static Builder|ProposalSnippet whereCss($value)
 * @method static Builder|ProposalSnippet whereDeletedAt($value)
 * @method static Builder|ProposalSnippet whereHtml($value)
 * @method static Builder|ProposalSnippet whereIcon($value)
 * @method static Builder|ProposalSnippet whereId($value)
 * @method static Builder|ProposalSnippet whereIsDeleted($value)
 * @method static Builder|ProposalSnippet whereName($value)
 * @method static Builder|ProposalSnippet wherePrivateNotes($value)
 * @method static Builder|ProposalSnippet whereProposalCategoryId($value)
 * @method static Builder|ProposalSnippet wherePublicId($value)
 * @method static Builder|ProposalSnippet whereUpdatedAt($value)
 * @method static Builder|ProposalSnippet whereUserId($value)
 * @method static Builder|ProposalSnippet withActiveOrSelected($id = false)
 * @method static Builder|ProposalSnippet withArchived()
 * @method static Builder|ProposalSnippet withTrashed()
 * @method static Builder|ProposalSnippet withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ProposalSnippet extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

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
    protected $presenter = ProposalSnippetPresenter::class;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL_SNIPPET;
    }

    public function getRoute(): string
    {
        return '/proposals/snippets/' . $this->public_id;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function proposal_category()
    {
        return $this->belongsTo(ProposalCategory::class)->withTrashed();
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
