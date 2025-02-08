<?php
namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class ExpenseCategory extends EntityModel
{
    // Expense Categories
    use SoftDeletes;
    use PresentableTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\EntityPresenter';

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_EXPENSE_CATEGORY;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense()
    {
        return $this->belongsTo('App\Models\Expense');
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/expense_categories/{$this->public_id}/edit";
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
