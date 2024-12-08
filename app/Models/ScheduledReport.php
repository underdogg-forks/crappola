<?php

namespace App\Models;

use Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Scheduled Report.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string                          $config
 * @property string                          $frequency
 * @property string                          $send_date
 * @property int|null                        $public_id
 * @property string|null                     $ip
 * @property \App\Models\Account             $account
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereSendDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ScheduledReport extends EntityModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'frequency',
        'config',
        'send_date',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function updateSendDate(): void
    {
        switch ($this->frequency) {
            case REPORT_FREQUENCY_DAILY:
                $this->send_date = Carbon::now()->addDay()->toDateString();
                break;
            case REPORT_FREQUENCY_WEEKLY:
                $this->send_date = Carbon::now()->addWeek()->toDateString();
                break;
            case REPORT_FREQUENCY_BIWEEKLY:
                $this->send_date = Carbon::now()->addWeeks(2)->toDateString();
                break;
            case REPORT_FREQUENCY_MONTHLY:
                $this->send_date = Carbon::now()->addMonth()->toDateString();
                break;
        }

        $this->save();
    }
}
