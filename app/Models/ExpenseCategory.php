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

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_EXPENSE_CATEGORY;
    }

    /**
     * @return BelongsTo
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
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
