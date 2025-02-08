<?php

namespace App\Models;

use App\Events\TaskWasCreated;
use App\Events\TaskWasUpdated;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Task.
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
    protected $presenter = TaskPresenter::class;

    public static function getTimeString($timestamp)
    {
        return Utils::timestampToDateTimeString($timestamp);
    }

    /**
     * @return array<int|string, mixed>
     */
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

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TASK;
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function task_status()
    {
        return $this->belongsTo(TaskStatus::class)->withTrashed();
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return self::calcStartTime($this);
    }

    /**
     * @return string
     */
    public static function calcStartTime($task)
    {
        $parts = json_decode($task->time_log) ?: [];

        if (count($parts)) {
            return Utils::timestampToDateTimeString($parts[0][0]);
        }

        return '';
    }

    public function getLastStartTime()
    {
        $parts = json_decode($this->time_log) ?: [];

        if (count($parts)) {
            $index = count($parts) - 1;

            return $parts[$index][0];
        }

        return '';
    }

    public function getTimeLog()
    {
        return $this->time_log;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        $value = 0;

        if ($this->product && $this->product->cost) {
            $value = $this->product->cost;
        } elseif ($this->project && floatval($this->project->task_rate)) {
            $value = $this->project->task_rate;
        } elseif ($this->client && floatval($this->client->task_rate)) {
            $value = $this->client->task_rate;
        } else {
            $value = $this->company->task_rate;
        }

        return Utils::roundSignificant($value);
    }

    /**
     * @return int
     */
    public function getCurrentDuration()
    {
        $parts = json_decode($this->time_log) ?: [];
        $part = $parts[count($parts) - 1];
        if (count($part) == 1) {
            return time() - $part[0];
        }
        if (! $part[1]) {
            return time() - $part[0];
        }

        return 0;
    }

    public function hasPreviousDuration(): bool
    {
        $parts = json_decode($this->time_log) ?: [];

        return count($parts) && (count($parts[0]) && $parts[0][1]);
    }

    public function getHours(): float
    {
        return round($this->getDuration() / (60 * 60), 2);
    }

    /**
     * @return int
     */
    public function getDuration($startTimeCutoff = 0, $endTimeCutoff = 0)
    {
        return self::calcDuration($this, $startTimeCutoff, $endTimeCutoff);
    }

    /**
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

    /**
     * Gets the route to the tasks edit action.
     *
     * @return string
     */
    public function getRoute()
    {
        return "/tasks/{$this->public_id}/edit";
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

    public function statusClass()
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

    public static function calcStatusClass($isRunning, $balance, $invoiceNumber): string
    {
        if ($invoiceNumber) {
            if (floatval($balance)) {
                return 'default';
            }

            return 'success';
        }
        if ($isRunning) {
            return 'primary';
        }

        return 'info';
    }

    public function statusLabel()
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Task::created(function ($task): void {
    event(new TaskWasCreated($task));
});

Task::updated(function ($task): void {
    event(new TaskWasUpdated($task));
});
