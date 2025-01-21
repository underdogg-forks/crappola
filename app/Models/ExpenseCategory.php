<?php

namespace App\Models;

use App\Ninja\Presenters\EntityPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int          $id
 * @property int          $user_id
 * @property int          $account_id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property string|null  $name
 * @property int          $public_id
 * @property int          $is_deleted
 * @property Expense|null $expense
 *
 * @method static Builder|ExpenseCategory newModelQuery()
 * @method static Builder|ExpenseCategory newQuery()
 * @method static Builder|ExpenseCategory onlyTrashed()
 * @method static Builder|ExpenseCategory query()
 * @method static Builder|ExpenseCategory scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|ExpenseCategory whereAccountId($value)
 * @method static Builder|ExpenseCategory whereCreatedAt($value)
 * @method static Builder|ExpenseCategory whereDeletedAt($value)
 * @method static Builder|ExpenseCategory whereId($value)
 * @method static Builder|ExpenseCategory whereIsDeleted($value)
 * @method static Builder|ExpenseCategory whereName($value)
 * @method static Builder|ExpenseCategory wherePublicId($value)
 * @method static Builder|ExpenseCategory whereUpdatedAt($value)
 * @method static Builder|ExpenseCategory whereUserId($value)
 * @method static Builder|ExpenseCategory withActiveOrSelected($id = false)
 * @method static Builder|ExpenseCategory withArchived()
 * @method static Builder|ExpenseCategory withTrashed()
 * @method static Builder|ExpenseCategory withoutTrashed()
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
    protected $presenter = EntityPresenter::class;

    public function getEntityType(): string
    {
        return ENTITY_EXPENSE_CATEGORY;
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function getRoute(): string
    {
        return sprintf('/expense_categories/%s/edit', $this->public_id);
    }
}
