<?php

namespace App\Models;

use App\Libraries\Utils;
use App\Ninja\Presenters\CompanyPlanPresenter;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class CompanyPlan.
 */
class CompanyPlan extends Model
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = CompanyPlanPresenter::class;

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

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'promo_expires',
        'discount_expires',
    ];

    /**
     * @return BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function discountedPrice($price)
    {
        if (! $this->hasActivePromo() && ! $this->hasActiveDiscount()) {
            return $price;
        }

        return $price - ($price * $this->discount);
    }

    public function hasActivePromo(): bool
    {
        if ($this->discount_expires) {
            return false;
        }

        return $this->promo_expires && $this->promo_expires->gte(Carbon::today());
    }

    // handle promos and discounts
    public function hasActiveDiscount(Carbon $date = null)
    {
        if (! $this->discount) {
            return false;
        }
        if (! $this->discount_expires) {
            return false;
        }
        $date = $date ?: Carbon::today();

        if ($this->plan_term == PLAN_TERM_MONTHLY) {
            return $this->discount_expires->gt($date);
        }

        return $this->discount_expires->subMonths(11)->gt($date);
    }

    public function daysUntilPlanExpires()
    {
        if (! $this->hasActivePlan()) {
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
        if (! Utils::isNinjaProd()) {
            return false;
        }
        if (Utils::isPro()) {
            return false;
        }
        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }
        // if they've already had a discount or a promotion is active return false
        if ($this->discount_expires) {
            return false;
        }
        if ($this->hasActivePromo()) {
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

    public function processRefund($user): bool
    {
        if (! $this->payment) {
            return false;
        }

        $company = $this->companies()->first();
        $planDetails = $company->getPlanDetails(false, false);

        if (! empty($planDetails['started'])) {
            $deadline = clone $planDetails['started'];
            $deadline->modify('+30 days');

            if ($deadline >= date_create()) {
                $companyRepo = app(AccountRepository::class);
                $ninjaAccount = $companyRepo->getNinjaAccount();
                $paymentDriver = $ninjaAccount->paymentDriver();
                $paymentDriver->refundPayment($this->payment);

                Log::info("Refunded Plan Payment: {$company->name} - {$user->email} - Deadline: {$deadline->format('Y-m-d')}");

                return true;
            }
        }

        return false;
    }

    /**
     * @return HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function getPlanDetails($includeInactive = false, $includeTrial = true)
    {
        $company = $this->companies()->first();

        if (! $company) {
            return false;
        }

        return $company->getPlanDetails($includeInactive, $includeTrial);
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

CompanyPlan::deleted(function ($companyPlan): void {
    if (! env('MULTI_DB_ENABLED')) {
        return;
    }

    $server = DbServer::whereName(config('database.default'))->firstOrFail();

    LookupCompanyPlan::deleteWhere([
        'company_id'   => $companyPlan->id,
        'db_server_id' => $server->id,
    ]);
});
