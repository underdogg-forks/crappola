<?php

namespace App\Models;

use App\Events\TaskWasCreated;
use App\Events\TaskWasUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Task.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $account_id
 * @property int|null                        $client_id
 * @property int|null                        $invoice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null                     $description
 * @property int                             $is_deleted
 * @property int                             $public_id
 * @property int                             $is_running
 * @property string|null                     $time_log
 * @property int|null                        $project_id
 * @property int|null                        $task_status_id
 * @property int                             $task_status_sort_order
 * @property string|null                     $custom_value1
 * @property string|null                     $custom_value2
 * @property \App\Models\Account             $account
 * @property \App\Models\Client|null         $client
 * @property \App\Models\Invoice|null        $invoice
 * @property \App\Models\Project|null        $project
 * @property \App\Models\TaskStatus|null     $task_status
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Task dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsRunning($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskStatusSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTimeLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Task withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Task extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'description',
        'time_log',
        'is_running',
        'custom_value1',
        'custom_value2',
    ];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\TaskPresenter::class;

    /**
     * @param $task
     *
     * @return string
     */
    public static function calcStartTime($task)
    {
        $parts = json_decode($task->time_log) ?: [];

        if (count($parts) > 0) {
            return Utils::timestampToDateTimeString($parts[0][0]);
        }

        return '';
    }

    /**
     * @param $task
     *
     * @return int
     */
    public static function calcDuration($task, $startTimeCutoff = 0, $endTimeCutoff = 0): float
    {
        $duration = 0;
        $parts = json_decode($task->time_log) ?: [];

        foreach ($parts as $part) {
            $startTime = $part[0];
            $endTime = count($part) == 1 || ! $part[1] ? time() : $part[1];

            if ($startTimeCutoff) {
                $startTime = max($startTime, $startTimeCutoff);
            }

            if ($endTimeCutoff) {
                $endTime = min($endTime, $endTimeCutoff);
            }

            $duration += max($endTime - $startTime, 0);
        }

        return round($duration);
    }

    public static function getStatuses($entityType = false): array
    {
        $statuses = [];

        $taskStatues = TaskStatus::scope()->orderBy('sort_order')->get();

        foreach ($taskStatues as $status) {
            $statuses[$status->public_id] = $status->name;
        }

        $statuses[TASK_STATUS_LOGGED] = trans('texts.logged');
        $statuses[TASK_STATUS_RUNNING] = trans('texts.running');
        $statuses[TASK_STATUS_INVOICED] = trans('texts.invoiced');
        $statuses[TASK_STATUS_PAID] = trans('texts.paid');

        return $statuses;
    }

    public static function calcStatusLabel($isRunning, $balance, $invoiceNumber, $taskStatus)
    {
        if ($invoiceNumber) {
            $label = (float) $balance > 0 ? trans('texts.invoiced') : trans('texts.paid');
        } elseif ($taskStatus) {
            $label = $taskStatus;
        } else {
            $label = trans('texts.logged');
        }

        if ($isRunning) {
            $label .= ' | ' . trans('texts.running');
        }

        return $label;
    }

    public static function calcStatusClass($isRunning, $balance, $invoiceNumber): string
    {
        if ($invoiceNumber) {
            if ((float) $balance !== 0.0) {
                return 'default';
            }

            return 'success';
        }

        if ($isRunning) {
            return 'primary';
        }

        return 'info';
    }

    public function getEntityType(): string
    {
        return ENTITY_TASK;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class)->withTrashed();
    }

    public function task_status()
    {
        return $this->belongsTo(\App\Models\TaskStatus::class)->withTrashed();
    }

    public function getStartTime()
    {
        return self::calcStartTime($this);
    }

    public function getLastStartTime()
    {
        $parts = json_decode($this->time_log) ?: [];

        if (count($parts) > 0) {
            $index = count($parts) - 1;

            return $parts[$index][0];
        }

        return '';
    }

    public function getDuration($startTimeCutoff = 0, $endTimeCutoff = 0)
    {
        return self::calcDuration($this, $startTimeCutoff, $endTimeCutoff);
    }

    /**
     * @return float
     */
    public function getRate()
    {
        $value = 0;

        if ($this->project && (float) ($this->project->task_rate)) {
            $value = $this->project->task_rate;
        } elseif ($this->client && (float) ($this->client->task_rate)) {
            $value = $this->client->task_rate;
        } else {
            $value = $this->account->task_rate;
        }

        return Utils::roundSignificant($value);
    }

    public function getCurrentDuration(): int|float
    {
        $parts = json_decode($this->time_log) ?: [];
        $part = $parts[count($parts) - 1];

        if (count($part) == 1 || ! $part[1]) {
            return time() - $part[0];
        }

        return 0;
    }

    public function hasPreviousDuration(): bool
    {
        $parts = json_decode($this->time_log) ?: [];

        return count($parts) && (count($parts[0]) && $parts[0][1]);
    }

    /**
     * @return float
     */
    public function getHours(): float
    {
        return round($this->getDuration() / (60 * 60), 2);
    }

    /**
     * Gets the route to the tasks edit action.
     *
     * @return string
     */
    public function getRoute(): string
    {
        return sprintf('/tasks/%s/edit', $this->public_id);
    }

    public function getName(): string
    {
        return '#' . $this->public_id;
    }

    public function getDisplayName()
    {
        if ($this->description) {
            return Utils::truncateString($this->description, 16);
        }

        return '#' . $this->public_id;
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        $query->whereRaw('cast(substring(time_log, 3, 10) as unsigned) <= ' . $endDate->modify('+1 day')->format('U'))
            ->whereRaw('case
                when is_running then unix_timestamp()
                else cast(substring(time_log, length(time_log) - 11, 10) as unsigned)
            end >= ' . $startDate->format('U'));

        return $query;
    }

    public function statusClass(): string
    {
        if ($this->invoice) {
            $balance = $this->invoice->balance;
            $invoiceNumber = $this->invoice->invoice_number;
        } else {
            $balance = 0;
            $invoiceNumber = false;
        }

        return static::calcStatusClass($this->is_running, $balance, $invoiceNumber);
    }

    public function statusLabel(): string
    {
        if ($this->invoice) {
            $balance = $this->invoice->balance;
            $invoiceNumber = $this->invoice->invoice_number;
        } else {
            $balance = 0;
            $invoiceNumber = false;
        }

        $taskStatus = $this->task_status ? $this->task_status->name : false;

        return static::calcStatusLabel($this->is_running, $balance, $invoiceNumber, $taskStatus);
    }
}

Task::created(function ($task): void {
    event(new TaskWasCreated($task));
});

Task::updated(function ($task): void {
    event(new TaskWasUpdated($task));
});
