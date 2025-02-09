<?php

namespace App\Ninja\Presenters;

use App\Libraries\Utils;
use stdClass;

/**
 * Class TaskPresenter.
 */
class TaskPresenter extends EntityPresenter
{
    public function client()
    {
        return $this->entity->client ? $this->entity->client->getDisplayName() : '';
    }

    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function description(): string
    {
        return mb_substr($this->entity->description, 0, 40) . (mb_strlen($this->entity->description) > 40 ? '...' : '');
    }

    public function project()
    {
        return $this->entity->project ? $this->entity->project->name : '';
    }

    /**
     * @param mixed $showProject
     *
     * @return mixed
     */
    public function invoiceDescription($account, $showProject): string
    {
        $str = '';

        if ($showProject && $project = $this->project()) {
            $str .= "## {$project}\n\n";
        }

        if (($description = trim($this->entity->description)) !== '' && ($description = trim($this->entity->description)) !== '0') {
            $str .= $description . "\n\n";
        }

        $parts = json_decode($this->entity->time_log) ?: [];
        $times = [];

        foreach ($parts as $part) {
            $start = $part[0];
            $end = count($part) == 1 || ! $part[1] ? time() : $part[1];

            $start = $account->formatDateTime('@' . (int) $start);
            $end = $account->formatTime('@' . (int) $end);

            $times[] = sprintf('### %s - %s', $start, $end);
        }

        return $str . implode("\n", $times);
    }

    public function calendarEvent($subColors = false): stdClass
    {
        $data = parent::calendarEvent();
        $task = $this->entity;
        $account = $task->account;
        $date = $account->getDateTime();

        $data->title = trans('texts.task');
        if ($project = $this->project()) {
            $data->title .= ' | ' . $project;
        }

        if (($description = $this->description()) !== '' && ($description = $this->description()) !== '0') {
            $data->title .= ' | ' . $description;
        }

        $data->allDay = false;

        if ($subColors && $task->project_id) {
            $data->borderColor = Utils::brewerColor($task->project->public_id);
            $data->backgroundColor = $data->borderColor;
        } else {
            $data->borderColor = '#a87821';
            $data->backgroundColor = '#a87821';
        }

        $parts = json_decode($task->time_log) ?: [];
        if (count($parts) > 0) {
            $first = $parts[0];
            $start = $first[0];
            $date->setTimestamp($start);
            $data->start = $date->format('Y-m-d H:i:m');

            $last = $parts[count($parts) - 1];
            $end = count($last) == 2 ? $last[1] : $last[0];
            $date->setTimestamp($end);
            $data->end = $date->format('Y-m-d H:i:m');
        }

        return $data;
    }
}
