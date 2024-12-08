<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null                     $name
 * @property int                             $public_id
 * @property int                             $is_deleted
 * @property \App\Models\Expense|null        $expense
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ExpenseCategory extends EntityModel
{
    use PresentableTrait;
    // Expense Categories
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\EntityPresenter::class;

    public function getEntityType(): string
    {
        return ENTITY_EXPENSE_CATEGORY;
    }

    public function expense()
    {
        return $this->belongsTo(\App\Models\Expense::class);
    }

    public function getRoute(): string
    {
        return sprintf('/expense_categories/%s/edit', $this->public_id);
    }
}
