<?php

namespace App\Ninja\Reports;

use App\Models\Activity;

class ActivityReport extends AbstractReport
{
    public function getColumns(): array
    {
        return [
            'date'     => [],
            'client'   => [],
            'user'     => [],
            'activity' => [],
        ];
    }

    public function run(): void
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $subgroup = $this->options['subgroup'];

        $activities = Activity::scope()
            ->with('client.contacts', 'user', 'invoice', 'payment', 'credit', 'task', 'expense', 'account')
            ->whereRaw(sprintf('DATE(created_at) >= "%s" and DATE(created_at) <= "%s"', $startDate, $endDate))
            ->orderBy('id', 'desc');

        foreach ($activities->get() as $activity) {
            $client = $activity->client;
            $this->data[] = [
                $activity->present()->createdAt,
                $client ? ($this->isExport ? $client->getDisplayName() : $client->present()->link) : '',
                $activity->present()->user,
                $this->isExport ? strip_tags($activity->getMessage()) : $activity->getMessage(),
            ];

            $dimension = $subgroup == 'category' ? trans('texts.' . $activity->relatedEntityType()) : $this->getDimension($activity);

            $this->addChartData($dimension, $activity->created_at, 1);
        }

        //dd($this->getChartData());
    }
}
