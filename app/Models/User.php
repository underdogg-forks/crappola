<?php

namespace App\Models;

use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Libraries\Utils;
use DateTimeInterface;
use Event;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;
use Session;

/**
 * Class User.
 */
class User extends Authenticatable
{
    use PresentableTrait;
    use SoftDeletes;
    use Notifiable;
    use HasAvatar;

    /**
     * @var array
     */
    public static $all_permissions = [
        'create_all' => 0b0001,
        'view_all' => 0b0010,
        'edit_all' => 0b0100,
    ];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\UserPresenter';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'signature',
        'avatar',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'confirmation_code',
        'oauth_user_id',
        'oauth_provider_id',
        'google_2fa_secret',
        'google_2fa_phone',
        'remember_2fa_token',
        'slack_webhook_url',
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @param $user
     */
    public static function onUpdatingUser($user): void
    {
        if ($user->password != $user->getOriginal('password')) {
            $user->failed_logins = 0;
        }

        // if the user changes their email then they need to reconfirm it
        if ($user->isEmailBeingChanged()) {
            $user->confirmed = 0;
            $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));
        }
    }

    /**
     * @return bool
     */
    public function isEmailBeingChanged()
    {
        return Utils::isNinjaProd() && $this->email != $this->getOriginal('email');
    }

    /**
     * @param $user
     */
    public static function onUpdatedUser($user): void
    {
        if (!$user->getOriginal('email')
            || $user->getOriginal('email') == TEST_USERNAME
            || $user->getOriginal('username') == TEST_USERNAME
            || $user->getOriginal('email') == 'tests@bitrock.com') {
            event(new UserSignedUp());
        }

        event(new UserSettingsChanged($user));
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return BelongsTo
     */
    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }

    /**
     * @param $value
     */
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $this->attributes['username'] = $value;
    }

    /**
     * @return mixed|string
     */
    public function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->getFullName()) {
            return $this->getFullName();
        } elseif ($this->email) {
            return $this->email;
        }

        return trans('texts.guest');
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->first_name || $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }

        return '';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getDisplayName();
    }

    /**
     * @return mixed
     */
    public function getPersonType()
    {
        return PERSON_USER;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function isEnterprise()
    {
        return $this->company->isEnterprise();
    }

    /**
     * @return mixed
     */
    public function isTrusted()
    {
        if (Utils::isSelfHost()) {
        }

        return $this->company->isPro() && !$this->company->isTrial();
    }

    /**
     * @return mixed
     */
    public function isPro()
    {
        return $this->company->isPro();
    }

    /**
     * @return mixed
     */
    public function isTrial()
    {
        return $this->company->isTrial();
    }

    /**
     * @return mixed
     */
    public function hasActivePromo()
    {
        return $this->company->hasActivePromo();
    }

    /**
     * @return int
     */
    public function maxInvoiceDesignId()
    {
        return $this->hasFeature(FEATURE_MORE_INVOICE_DESIGNS) ? 13 : COUNT_FREE_DESIGNS;
    }

    /**
     * @param $feature
     *
     * @return mixed
     */
    public function hasFeature($feature)
    {
        return $this->company->hasFeature($feature);
    }

    /**
     * @return bool
     */
    public function showGreyBackground()
    {
        return !$this->theme_id || in_array($this->theme_id, [2, 3, 5, 6, 7, 8, 10, 11, 12]);
    }

    /**
     * @return mixed
     */
    public function getRequestsCount()
    {
        return Session::get(SESSION_COUNTER, 0);
    }

    /**
     * @param bool $success
     * @param bool $forced
     *
     * @return bool
     */
    public function afterSave($success = true, $forced = false)
    {
        if ($this->email) {
            return parent::afterSave($success = true, $forced = false);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getMaxNumClients()
    {
        /*if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_CLIENTS_PRO;
        }

        if ($this->id < LEGACY_CUTOFF) {
            return MAX_NUM_CLIENTS_LEGACY;
        }*/

        return 40000;
    }

    /**
     * @return mixed
     */
    public function getMaxNumVendors()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_VENDORS_PRO;
        }

        return MAX_NUM_VENDORS;
    }

    public function clearSession(): void
    {
        $keys = [
            SESSION_USER_ACCOUNTS,
            SESSION_TIMEZONE,
            SESSION_DATE_FORMAT,
            SESSION_DATE_PICKER_FORMAT,
            SESSION_DATETIME_FORMAT,
            SESSION_CURRENCY,
            SESSION_LOCALE,
        ];

        foreach ($keys as $key) {
            Session::forget($key);
        }
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    public function owns($entity)
    {
        return !empty($entity->user_id) && $entity->user_id == $this->id;
    }

    public function isAgent($entity)
    {
        return $entity->agent_id == $this->id;
    }

    /**
     * @return bool|mixed
     */
    public function filterId()
    {   //todo permissions
        return $this->hasPermission('view_all') ? false : $this->id;
    }

    /**
     * Checks to see if the user has the required permission.
     *
     * @param mixed $permission Either a single permission or an array of possible permissions
     * @param mixed $requireAll - True to require all permissions, false to require only one
     *
     * @return bool
     */
    public function hasPermission($permission, $requireAll = false)
    {
        if ($this->is_admin) {
            return true;
        } elseif (is_string($permission)) {
            if (is_array(json_decode($this->permissions, 1)) && in_array($permission, json_decode($this->permissions, 1))) {
                return true;
            }
        } elseif (is_array($permission)) {
            if ($requireAll) {
                return count(array_intersect($permission, json_decode($this->permissions, 1))) == count($permission);
            }

            return count(array_intersect($permission, json_decode($this->permissions, 1))) > 0;
        }

        return false;
    }

    /**
     * @param $entity
     *
     * @return bool|mixed
     */
    public function filterIdByEntity($entity)
    {
        return $this->hasPermission('view_' . $entity) ? false : $this->id;
    }

    /**
     * @return bool
     */
    public function caddAddUsers()
    {
        if (!Utils::isNinjaProd()) {
            return true;
        } elseif (!$this->hasFeature(FEATURE_USERS)) {
            return false;
        }

        $company = $this->company;
        $companyPlan = $company->companyPlan;

        $numUsers = 1;
        foreach ($companyPlan->accounts as $company) {
            $numUsers += $company->users->count() - 1;
        }

        return $numUsers < $companyPlan->num_users;
    }

    /**
     * @param $entityType
     * @param bool $entity
     *
     * @return bool
     */
    public function canCreateOrEdit($entityType, $entity = false)
    {
        if ($entity) {
            return ($entity && $this->can('edit', $entity)) || ($entity && $this->can('create', $entity));
        } elseif ($entityType) {
            return $this->hasPermission('edit_' . $entityType) || $this->hasPermission('create_' . $entityType);
        }
    }

    /**
     * @return mixed
     */
    public function primaryAccount()
    {
        return $this->company->companyPlan->accounts->sortBy('id')->first();
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token): void
    {
        //$this->notify(new ResetPasswordNotification($token));
        app('App\Ninja\Mailers\UserMailer')->sendPasswordReset($this, $token);
    }

    /**
     * @return mixed
     */
    public function routeNotificationForSlack()
    {
        return $this->slack_webhook_url;
    }

    /**
     * @return bool
     */
    public function hasAcceptedLatestTerms()
    {
        if (!NINJA_TERMS_VERSION) {
            return true;
        }

        return $this->accepted_terms_version == NINJA_TERMS_VERSION;
    }

    /**
     * @param $ip
     *
     * @return $this
     */
    public function acceptLatestTerms($ip)
    {
        $this->accepted_terms_version = NINJA_TERMS_VERSION;
        $this->accepted_terms_timestamp = date('Y-m-d H:i:s');
        $this->accepted_terms_ip = $ip;

        return $this;
    }

    /**
     * @param $invoice
     *
     * @return bool
     */
    public function shouldNotify($invoice)
    {
        if (!$this->email || !$this->confirmed) {
            return false;
        }

        if ($this->cannot('view', $invoice)) {
            return false;
        }

        if ($this->only_notify_owned && !$this->ownsEntity($invoice)) {
            return false;
        }

        return true;
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    public function ownsEntity($entity)
    {
        return $entity->user_id == $this->id;
    }

    public function permissionsMap()
    {
        $data = [];
        $permissions = json_decode($this->permissions);

        if (!$permissions) {
            return $data;
        }

        $keys = array_values((array)$permissions);
        $values = array_fill(0, count($keys), true);

        return array_combine($keys, $values);
    }

    public function isTicketMaster()
    {
        return $this->id == $this->company->account_ticket_settings->ticket_master_id;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

User::created(function ($user): void {
    LookupUser::createNew($user->company->account_key, [
        'email' => $user->email,
        'user_id' => $user->id,
        'confirmation_code' => $user->confirmation_code,
    ]);
});

User::updating(function ($user): void {
    User::onUpdatingUser($user);

    $dirty = $user->getDirty();
    if (array_key_exists('email', $dirty)
        || array_key_exists('confirmation_code', $dirty)
        || array_key_exists('oauth_user_id', $dirty)
        || array_key_exists('oauth_provider_id', $dirty)
        || array_key_exists('referral_code', $dirty)) {
        LookupUser::updateUser($user->company->account_key, $user);
    }
});

User::updated(function ($user): void {
    User::onUpdatedUser($user);
});

User::deleted(function ($user): void {
    if (!$user->email) {
        return;
    }

    if ($user->forceDeleting) {
        LookupUser::deleteWhere([
            'email' => $user->email,
        ]);
    }
});
