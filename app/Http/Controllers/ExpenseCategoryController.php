<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseCategoryRequest;
use App\Http\Requests\ExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Ninja\Datatables\ExpenseCategoryDatatable;
use App\Ninja\Repositories\ExpenseCategoryRepository;
use App\Services\ExpenseCategoryService;

class ExpenseCategoryController extends BaseController
{
    protected \App\Ninja\Repositories\ExpenseCategoryRepository $categoryRepo;

    protected \App\Services\ExpenseCategoryService $categoryService;

    protected $entityType = ENTITY_EXPENSE_CATEGORY;

    public function __construct(ExpenseCategoryRepository $categoryRepo, ExpenseCategoryService $categoryService)
    {
        $this->categoryRepo = $categoryRepo;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \Illuminate\Support\Facades\View::make('list_wrapper', [
            'entityType' => ENTITY_EXPENSE_CATEGORY,
            'datatable'  => new ExpenseCategoryDatatable(),
            'title'      => trans('texts.expense_categories'),
        ]);
    }

    public function getDatatable($expensePublicId = null)
    {
        return $this->categoryService->getDatatable(\Illuminate\Support\Facades\Request::input('sSearch'));
    }

    public function create(ExpenseCategoryRequest $request)
    {
        $data = [
            'category' => null,
            'method'   => 'POST',
            'url'      => 'expense_categories',
            'title'    => trans('texts.new_category'),
        ];

        return \Illuminate\Support\Facades\View::make('expense_categories.edit', $data);
    }

    public function edit(ExpenseCategoryRequest $request)
    {
        $category = $request->entity();

        $data = [
            'category' => $category,
            'method'   => 'PUT',
            'url'      => 'expense_categories/' . $category->public_id,
            'title'    => trans('texts.edit_category'),
        ];

        return \Illuminate\Support\Facades\View::make('expense_categories.edit', $data);
    }

    public function store(CreateExpenseCategoryRequest $request)
    {
        $category = $this->categoryRepo->save($request->input());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.created_expense_category'));

        return redirect()->to($category->getRoute());
    }

    public function update(UpdateExpenseCategoryRequest $request)
    {
        $category = $this->categoryRepo->save($request->input(), $request->entity());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_expense_category'));

        return redirect()->to($category->getRoute());
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('action');
        $ids = \Illuminate\Support\Facades\Request::input('public_id') ?: \Illuminate\Support\Facades\Request::input('ids');
        $count = $this->categoryService->bulk($ids, $action);

        if ($count > 0) {
            $field = $count == 1 ? "{$action}d_expense_category" : "{$action}d_expense_categories";
            $message = trans("texts.{$field}", ['count' => $count]);
            \Illuminate\Support\Facades\Session::flash('message', $message);
        }

        return redirect()->to('/expense_categories');
    }
}
