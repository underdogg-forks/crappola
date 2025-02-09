<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\ProjectTaskDatatable;
use App\Ninja\Datatables\TaskDatatable;
use App\Ninja\Repositories\TaskRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class TaskService.
 */
class TaskService extends BaseService
{
    protected DatatableService $datatableService;

    protected TaskRepository $taskRepo;

    /**
     * TaskService constructor.
     *
     * @param TaskRepository   $taskRepo
     * @param DatatableService $datatableService
     */
    public function __construct(TaskRepository $taskRepo, DatatableService $datatableService)
    {
        $this->taskRepo = $taskRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $clientPublicId
     * @param $search
     *
     * @return JsonResponse
     */
    public function getDatatable($clientPublicId, $projectPublicId, $search)
    {
        if ($projectPublicId) {
            $datatable = new ProjectTaskDatatable(true, true);
        } else {
            $datatable = new TaskDatatable(true, $clientPublicId);
        }

        $query = $this->taskRepo->find($clientPublicId, $projectPublicId, $search);

        if ( ! Utils::hasPermission('view_task')) {
            $query->where('tasks.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return TaskRepository
     */
    protected function getRepo(): TaskRepository
    {
        return $this->taskRepo;
    }
}
