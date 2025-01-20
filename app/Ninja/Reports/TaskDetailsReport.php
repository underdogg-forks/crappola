<?php

namespace App\Ninja\Reports;

use App\Models\Task;
use Auth;
use App\Libraries\Utils;

class TaskDetailsReport extends AbstractReport
{
    public function getColumns()
    {
        $columns = [
            'client' => [],
            'start_date' => [],
            'project' => [],
            'description' => [],
            'duration' => [],
            'amount' => [],
            'user' => ['columnSelector-false'],
            'time_log' => [],
        ];

        $user = auth()->user();
        $company = $user->company;

        if ($company->customLabel('task1')) {
            $columns[$company->present()->customLabel('task1')] = ['columnSelector-false', 'custom'];
        }
        if ($company->customLabel('task2')) {
            $columns[$company->present()->customLabel('task2')] = ['columnSelector-false', 'custom'];
        }

        return $columns;
    }

    public function run(): void
    {
        $company = Auth::user()->company;
        $startDate = date_create($this->startDate);
        $endDate = date_create($this->endDate);
        $subgroup = $this->options['subgroup'];
        $tasks = Task::scope()
            ->orderBy('created_at', 'desc')
            ->with('client.contacts', 'project', 'company', 'user')
            ->withArchived()
            ->dateRange($startDate, $endDate);
        foreach ($tasks->get() as $task) {
            $duration = $task->getDuration($startDate->format('U'), $endDate->modify('+1 day')->format('U'));
            $amount = $task->getRate() * ($duration / 60 / 60);
            if ($task->client && $task->client->currency_id) {
                $currencyId = $task->client->currency_id;
            } else {
                $currencyId = auth()->user()->company->getCurrencyId();
            }
            $logs = explode(']', $task->getTimeLog());
            $str2 = '';
            foreach ($logs as $log) {
                $str = str_replace(['[', ']'], '', $log);
                $str2 = $str2 . $str;
            }
            $str3 = explode(',', $str2);
            $i = 0;

            while ($i < sizeof($str3)) {
                $str2 = '';
                $str2 = date('d-m-Y,H:i:s', $str3[$i]) . ',' . date('d-m-Y,H:i:s', $str3[$i + 1]);
                $row = [
                    $task->client ? ($this->isExport ? $task->client->getDisplayName() : $task->client->present()->link) : trans('texts.unassigned'),
                    $this->isExport ? $task->getStartTime() : link_to($task->present()->url, $task->getStartTime()),
                    $task->present()->project,
                    $task->description,
                    Utils::formatTime($duration),
                    Utils::formatMoney($amount, $currencyId),
                    $task->user->getDisplayName(),
                    $str2,
                ];
                if ($company->customLabel('task1')) {
                    $row[] = $task->custom_value1;
                }
                if ($company->customLabel('task2')) {
                    $row[] = $task->custom_value2;
                }

                $this->data[] = $row;
                $i = $i + 2;
            }

            $this->addToTotals($currencyId, 'duration', $duration);
            $this->addToTotals($currencyId, 'amount', $amount);

            if ($subgroup == 'project') {
                $dimension = $task->present()->project;
            } else {
                $dimension = $this->getDimension($task);
            }
            $this->addChartData($dimension, $task->created_at, round($duration / 60 / 60, 2));
        }

        $data2 = $this->data;
    }
}
