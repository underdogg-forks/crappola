<?php

namespace App\Models;

use Carbon;
use DateTimeInterface;
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
 * @property Account                         $account
 * @property User                            $user
 *
 * @method static Builder|ScheduledReport newModelQuery()
 * @method static Builder|ScheduledReport newQuery()
 * @method static Builder|ScheduledReport onlyTrashed()
 * @method static Builder|ScheduledReport query()
 * @method static Builder|ScheduledReport scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|ScheduledReport whereAccountId($value)
 * @method static Builder|ScheduledReport whereConfig($value)
 * @method static Builder|ScheduledReport whereCreatedAt($value)
 * @method static Builder|ScheduledReport whereDeletedAt($value)
 * @method static Builder|ScheduledReport whereFrequency($value)
 * @method static Builder|ScheduledReport whereId($value)
 * @method static Builder|ScheduledReport whereIp($value)
 * @method static Builder|ScheduledReport wherePublicId($value)
 * @method static Builder|ScheduledReport whereSendDate($value)
 * @method static Builder|ScheduledReport whereUpdatedAt($value)
 * @method static Builder|ScheduledReport whereUserId($value)
 * @method static Builder|ScheduledReport withActiveOrSelected($id = false)
 * @method static Builder|ScheduledReport withArchived()
 * @method static Builder|ScheduledReport withTrashed()
 * @method static Builder|ScheduledReport withoutTrashed()
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
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
