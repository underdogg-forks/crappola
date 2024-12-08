<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                               $id
 * @property int                               $account_id
 * @property int                               $user_id
 * @property \Illuminate\Support\Carbon|null   $created_at
 * @property \Illuminate\Support\Carbon|null   $updated_at
 * @property \Illuminate\Support\Carbon|null   $deleted_at
 * @property int                               $is_deleted
 * @property int|null                          $proposal_category_id
 * @property string                            $name
 * @property string                            $icon
 * @property string                            $private_notes
 * @property string                            $html
 * @property string                            $css
 * @property int                               $public_id
 * @property \App\Models\Account               $account
 * @property \App\Models\ProposalCategory|null $proposal_category
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereProposalCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalSnippet withoutTrashed()
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
    protected $presenter = \App\Ninja\Presenters\ProposalSnippetPresenter::class;

    protected $casts = ['deleted_at' => 'datetime'];

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

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

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
