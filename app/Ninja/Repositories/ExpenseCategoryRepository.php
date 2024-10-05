<?php

namespace App\Ninja\Repositories;

use App\Models\ExpenseCategory;

class ExpenseCategoryRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return \App\Models\ExpenseCategory::class;
    }

    public function all()
    {
        return ExpenseCategory::scope()->get();
    }

    public function find($filter = null)
    {
        $query = \Illuminate\Support\Facades\DB::table('expense_categories')
            ->where('expense_categories.account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
            ->select(
                'expense_categories.name as category',
                'expense_categories.public_id',
                'expense_categories.user_id',
                'expense_categories.deleted_at',
                'expense_categories.is_deleted'
            );

        $this->applyFilters($query, ENTITY_EXPENSE_CATEGORY);

        if ($filter) {
            $query->where(function ($query) use ($filter): void {
                $query->where('expense_categories.name', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($input, $category = false)
    {
        $publicId = $data['public_id'] ?? false;

        if ( ! $category) {
            $category = ExpenseCategory::createNew();
        }

        $category->fill($input);
        $category->save();

        return $category;
    }
}
