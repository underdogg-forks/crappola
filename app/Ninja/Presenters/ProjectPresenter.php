<?php

namespace App\Ninja\Presenters;

use stdClass;
use Utils;

class ProjectPresenter extends EntityPresenter
{
    public function calendarEvent($subColors = false): stdClass
    {
        $data = parent::calendarEvent();
        $project = $this->entity;

        $data->title = trans('texts.project') . ': ' . $project->name;
        $data->start = $project->due_date;

        if ($subColors) {
            $data->borderColor = Utils::brewerColor($project->public_id);
            $data->backgroundColor = $data->borderColor;
        } else {
            $data->borderColor = '#676767';
            $data->backgroundColor = '#676767';
        }

        return $data;
    }

    public function taskRate()
    {
        if ((float) ($this->entity->task_rate) !== 0.0) {
            return Utils::roundSignificant($this->entity->task_rate);
        }

        return '';
    }

    public function defaultTaskRate()
    {
        if ($rate = $this->taskRate()) {
            return $rate;
        }

        return $this->entity->client->present()->defaultTaskRate;
    }
}
