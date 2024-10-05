<?php

namespace App\Ninja\Repositories;

use App\Models\Project;
use Utils;

class ProjectRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return \App\Models\Project::class;
    }

    public function all()
    {
        return Project::scope()->get();
    }

    public function find($filter = null, $userId = false)
    {
        $query = \Illuminate\Support\Facades\DB::table('projects')
            ->where('projects.account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
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
                \Illuminate\Support\Facades\DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'clients.user_id as client_user_id',
                'clients.public_id as client_public_id'
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

        if ( ! $project) {
            $project = Project::createNew();
            $project['client_id'] = $input['client_id'];
        }

        $project->fill($input);

        if (isset($input['due_date'])) {
            $project->due_date = Utils::toSqlDate($input['due_date']);
        }

        $project->save();

        return $project;
    }
}
