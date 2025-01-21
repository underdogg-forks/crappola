<?php

namespace App\Jobs;

use DateInterval;
use DatePeriod;
use stdClass;

class GenerateProjectChartData extends Job
{
    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): stdClass
    {
        $project = $this->project;
        $company = $project->company;
        $taskMap = [];
        $startTimestamp = time();
        $endTimestamp = max(time(), strtotime($project->due_date));
        $count = 0;
        $duration = 0;

        foreach ($project->tasks as $task) {
            $parts = json_decode($task->time_log) ?: [];

            if (! count($parts)) {
                continue;
            }

            $count++;

            foreach ($parts as $part) {
                $start = $part[0];
                $end = (count($part) > 1 && $part[1]) ? $part[1] : time();

                $date = $company->getDateTime();
                $date->setTimestamp($part[0]);
                $sqlDate = $date->format('Y-m-d');

                if (! isset($taskMap[$sqlDate])) {
                    $taskMap[$sqlDate] = 0;
                }

                $taskMap[$sqlDate] += $end - $start;
                $duration += $end - $start;
                $startTimestamp = min($startTimestamp, $start);
                $endTimestamp = max($endTimestamp, $end);
            }
        }

        $labels = [];
        $records = [];
        $startDate = $company->getDateTime()->setTimestamp($startTimestamp);
        $endDate = $company->getDateTime()->setTimestamp($endTimestamp);

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate);
        $data = [];
        $total = 0;
        $color = '51,122,183';

        foreach ($period as $date) {
            $labels[] = $date->format('m/d/Y');
            $sqlDate = $date->format('Y-m-d');

            if (isset($taskMap[$sqlDate])) {
                $total += round($taskMap[$sqlDate] / 60 / 60, 2);
            }

            $records[] = $total;
        }

        $dataset = new stdClass();
        $dataset->data = $records;
        $dataset->label = trans('texts.tasks');
        $dataset->lineTension = 0;
        $dataset->borderWidth = 4;
        $dataset->borderColor = "rgba({$color}, 1)";
        $dataset->backgroundColor = "rgba({$color}, 0.1)";

        $data = new stdClass();
        $data->labels = $labels;
        $data->datasets = [$dataset];
        $data->count = $count;
        $data->duration = $duration;

        return $data;
    }
}
