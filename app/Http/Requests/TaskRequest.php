<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\Project;
use App\Ninja\Repositories\ProjectRepository;

class TaskRequest extends EntityRequest
{
    public $entityType = ENTITY_TASK;

    public function sanitize()
    {
        $input = $this->all();

        /*
        // check if we're creating a new client
        if ($this->client_id == '-1') {
            $client = [
                'name' => trim($this->client_name),
            ];
            if (Client::validate($client) === true) {
                $client = app(\App\Ninja\Repositories\ClientRepository::class)->save($client);
                $input['client_id'] = $this->client_id = $client->public_id;
            }
        }
        */

        // check if we're creating a new project
        if ($this->project_id == '-1') {
            $project = [
                'name'      => trim($this->project_name),
                'client_id' => Client::getPrivateId($this->client_id ?: $this->client),
            ];
            if (Project::validate($project) === true) {
                $project = app(ProjectRepository::class)->save($project);
                $input['project_id'] = $project->public_id;
            } else {
                $input['project_id'] = null;
            }
        }

        $this->replace($input);

        return $this->all();
    }
}
