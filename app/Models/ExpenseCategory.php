<?php

namespace App\Models;

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
    protected $presenter = \App\Ninja\Presenters\EntityPresenter::class;

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_EXPENSE_CATEGORY;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense()
    {
        return $this->belongsTo(\App\Models\Expense::class);
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return sprintf('/expense_categories/%s/edit', $this->public_id);
    }
}
