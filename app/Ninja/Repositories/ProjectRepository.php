<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\Project';
    }

    public function all()
    {
        return Project::scope()->get();
    }

    public function find($filter = null, $userId = false)
    {
        $query = DB::table('projects')
            ->where('projects.company_id', '=', Auth::user()->company_id)
            ->join('companies', 'companies.id', '=', 'projects.company_id')
            ->leftjoin('clients', 'clients.id', '=', 'projects.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->where('contacts.deleted_at', '=', null)
            ->where('clients.deleted_at', '=', null)
            ->where(function ($query): void { // handle when client isn't set
                $query->where('contacts.is_primary', '=', true)
                    ->orWhere('contacts.is_primary', '=', null);
            })
            ->select(
                'projects.name as project',
                'projects.public_id',
                'projects.user_id',
                'projects.deleted_at',
                'projects.task_rate',
                'projects.is_deleted',
                'projects.due_date',
                'projects.budgeted_hours',
                'projects.private_notes',
                DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'clients.user_id as client_user_id',
                'clients.public_id as client_public_id',
                'clients.task_rate as client_task_rate',
                'companies.task_rate as account_task_rate'
            );

        $this->applyFilters($query, ENTITY_PROJECT);

        if ($filter) {
            $query->where(function ($query) use ($filter): void {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('projects.name', 'like', '%' . $filter . '%');
            });
        }

        if ($userId) {
            $query->where('projects.user_id', '=', $userId);
        }

        return $query;
    }

    public function save($input, $project = false)
    {
        $publicId = $data['public_id'] ?? false;

        if (! $project) {
            $project = Project::createNew();
            $project['client_id'] = $input['client_id'];
        }

        $project->fill($input);

        if (isset($input['due_at'])) {
            $project->due_date = Utils::toSqlDate($input['due_at']);
        }

        $project->save();

        return $project;
    }
}
