<?php

namespace App\Models;

use App\Libraries\Utils;
use App\Ninja\Presenters\CompanyPresenter;
use App\Ninja\Repositories\AccountRepository;
use Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Company.
 *
 * @property int                             $id
 * @property string|null                     $plan
 * @property string|null                     $plan_term
 * @property string|null                     $plan_started
 * @property string|null                     $plan_paid
 * @property string|null                     $plan_expires
 * @property int|null                        $payment_id
 * @property string|null                     $trial_started
 * @property string|null                     $trial_plan
 * @property string|null                     $pending_plan
 * @property string|null                     $pending_term
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null                     $plan_price
 * @property string|null                     $pending_plan_price
 * @property int                             $num_users
 * @property int                             $pending_num_users
 * @property string|null                     $utm_source
 * @property string|null                     $utm_medium
 * @property string|null                     $utm_campaign
 * @property string|null                     $utm_term
 * @property string|null                     $utm_content
 * @property float                           $discount
 * @property \Illuminate\Support\Carbon|null $discount_expires
 * @property \Illuminate\Support\Carbon|null $promo_expires
 * @property string|null                     $bluevine_status
 * @property string|null                     $referral_code
 * @property Collection<int, Account>        $accounts
 * @property int|null                        $accounts_count
 * @property Payment|null                    $payment
 *
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company onlyTrashed()
 * @method static Builder|Company query()
 * @method static Builder|Company whereBluevineStatus($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereDeletedAt($value)
 * @method static Builder|Company whereDiscount($value)
 * @method static Builder|Company whereDiscountExpires($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereNumUsers($value)
 * @method static Builder|Company wherePaymentId($value)
 * @method static Builder|Company wherePendingNumUsers($value)
 * @method static Builder|Company wherePendingPlan($value)
 * @method static Builder|Company wherePendingPlanPrice($value)
 * @method static Builder|Company wherePendingTerm($value)
 * @method static Builder|Company wherePlan($value)
 * @method static Builder|Company wherePlanExpires($value)
 * @method static Builder|Company wherePlanPaid($value)
 * @method static Builder|Company wherePlanPrice($value)
 * @method static Builder|Company wherePlanStarted($value)
 * @method static Builder|Company wherePlanTerm($value)
 * @method static Builder|Company wherePromoExpires($value)
 * @method static Builder|Company whereReferralCode($value)
 * @method static Builder|Company whereTrialPlan($value)
 * @method static Builder|Company whereTrialStarted($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company whereUtmCampaign($value)
 * @method static Builder|Company whereUtmContent($value)
 * @method static Builder|Company whereUtmMedium($value)
 * @method static Builder|Company whereUtmSource($value)
 * @method static Builder|Company whereUtmTerm($value)
 * @method static Builder|Company withTrashed()
 * @method static Builder|Company withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Company extends Model
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = CompanyPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'plan',
        'plan_term',
        'plan_price',
        'plan_paid',
        'plan_started',
        'plan_expires',
    ];

    protected $casts = ['deleted_at' => 'datetime', 'promo_expires' => 'datetime', 'discount_expires' => 'datetime'];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function hasActivePromo()
    {
        if ($this->discount_expires) {
            return false;
        }

        return $this->promo_expires && $this->promo_expires->gte(Carbon::today());
    }

    // handle promos and discounts
    public function hasActiveDiscount(?Carbon $date = null)
    {
        if ( ! $this->discount || ! $this->discount_expires) {
            return false;
        }

        $date = $date ?: Carbon::today();

        if ($this->plan_term == PLAN_TERM_MONTHLY) {
            return $this->discount_expires->gt($date);
        }

        return $this->discount_expires->subMonths(11)->gt($date);
    }

    public function discountedPrice($price)
    {
        if ( ! $this->hasActivePromo() && ! $this->hasActiveDiscount()) {
            return $price;
        }

        return $price - ($price * $this->discount);
    }

    public function daysUntilPlanExpires()
    {
        if ( ! $this->hasActivePlan()) {
            return 0;
        }

        return Carbon::parse($this->plan_expires)->diffInDays(Carbon::today());
    }

    public function hasActivePlan(): bool
    {
        return $this->plan_expires && Carbon::parse($this->plan_expires) >= Carbon::today();
    }

    public function hasExpiredPlan($plan)
    {
        if ($this->plan != $plan) {
            return false;
        }

        return Carbon::parse($this->plan_expires) < Carbon::today();
    }

    public function hasEarnedPromo(): bool
    {
        if ( ! Utils::isNinjaProd() || Utils::isPro()) {
            return false;
        }

        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }

        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }

        // if they've already had a discount or a promotion is active return false
        if ($this->discount_expires || $this->hasActivePromo()) {
            return false;
        }

        $discounts = [
            52 => [.6, 3],
            16 => [.4, 3],
            10 => [.25, 5],
        ];

        foreach ($discounts as $weeks => $promo) {
            [$discount, $validFor] = $promo;
            $difference = $this->created_at->diffInWeeks();
            if ($difference >= $weeks && $discount > $this->discount) {
                $this->discount = $discount;
                $this->promo_expires = date_create()->modify($validFor . ' days')->format('Y-m-d');
                $this->save();

                return true;
            }
        }

        return false;
    }

    public function getPlanDetails($includeInactive = false, $includeTrial = true)
    {
        $account = $this->accounts()->first();

        if ( ! $account) {
            return false;
        }

        return $account->getPlanDetails($includeInactive, $includeTrial);
    }

    public function processRefund($user): bool
    {
        if ( ! $this->payment) {
            return false;
        }

        $account = $this->accounts()->first();
        $planDetails = $account->getPlanDetails(false, false);

        if ( ! empty($planDetails['started'])) {
            $deadline = clone $planDetails['started'];
            $deadline->modify('+30 days');

            if ($deadline >= date_create()) {
                $accountRepo = app(AccountRepository::class);
                $ninjaAccount = $accountRepo->getNinjaAccount();
                $paymentDriver = $ninjaAccount->paymentDriver();
                $paymentDriver->refundPayment($this->payment);

                Log::info(sprintf('Refunded Plan Payment: %s - %s - Deadline: %s', $account->name, $user->email, $deadline->format('Y-m-d')));

                return true;
            }
        }

        return false;
    }

    public function applyDiscount($amount): void
    {
        $this->discount = $amount;
        $this->promo_expires = date_create()->modify('3 days')->format('Y-m-d');
    }

    public function applyFreeYear($numYears = 1): void
    {
        if ($this->plan_started && $this->plan_started != '0000-00-00') {
            return;
        }

        $this->plan = PLAN_PRO;
        $this->plan_term = PLAN_TERM_YEARLY;
        $this->plan_price = PLAN_PRICE_PRO_MONTHLY;
        $this->plan_started = date_create()->format('Y-m-d');
        $this->plan_paid = date_create()->format('Y-m-d');
        $this->plan_expires = date_create()->modify($numYears . ' year')->format('Y-m-d');
    }
}

Company::deleted(function ($company): void {
    if ( ! env('MULTI_DB_ENABLED')) {
        return;
    }

    $server = DbServer::whereName(config('database.default'))->firstOrFail();

    LookupCompany::deleteWhere([
        'company_id'   => $company->id,
        'db_server_id' => $server->id,
    ]);
});
