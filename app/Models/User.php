<?php

namespace App\Models;

use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Libraries\Utils;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laracasts\Presenter\PresentableTrait;

class User extends Authenticatable
{
    use Notifiable;
    use PresentableTrait;
    use SoftDeletes;

    public static $all_permissions = [
        'create_all' => 0b0001,
        'view_all'   => 0b0010,
        'edit_all'   => 0b0100,
    ];

    protected $presenter = 'App\Ninja\Presenters\UserPresenter';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
    ];

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

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public static function onUpdatingUser($user)
    {
        if ($user->password != $user->getOriginal('password')) {
            $user->failed_logins = 0;
        }

        // if the user changes their email then they need to reconfirm it
        if ($user->isEmailBeingChanged()) {
            $user->confirmed = 0;
            $user->confirmation_code = mb_strtolower(str_random(RANDOM_KEY_LENGTH));
        }
    }

    public static function onUpdatedUser($user)
    {
        if (
            ! $user->getOriginal('email')
            || $user->getOriginal('email') == TEST_USERNAME
            || $user->getOriginal('username') == TEST_USERNAME
            || $user->getOriginal('email') == 'tests@bitrock.com'
        ) {
            event(new UserSignedUp());
        }

        event(new UserSettingsChanged($user));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $this->attributes['username'] = $value;
    }

    public function getName()
    {
        return $this->getDisplayName();
    }

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

    public function isPro()
    {
        return $this->account->isPro();
    }

    public function isEnterprise()
    {
        return $this->account->isEnterprise();
    }

    public function isTrusted()
    {
        if (Utils::isSelfHost()) {
        }

        return $this->account->isPro() && ! $this->account->isTrial();
    }

    public function hasActivePromo()
    {
        return $this->account->hasActivePromo();
    }

    /**
     * @param $feature
     *
     * @return mixed
     */
    public function hasFeature($feature)
    {
        return $this->account->hasFeature($feature);
    }

    public function isTrial()
    {
        return $this->account->isTrial();
    }

    public function maxInvoiceDesignId()
    {
        return $this->hasFeature(FEATURE_MORE_INVOICE_DESIGNS) ? 13 : COUNT_FREE_DESIGNS;
    }

    public function getDisplayName()
    {
        if ($this->getFullName()) {
            return $this->getFullName();
        }
        if ($this->email) {
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
     * @return bool
     */
    public function showGreyBackground()
    {
        return ! $this->theme_id || in_array($this->theme_id, [2, 3, 5, 6, 7, 8, 10, 11, 12]);
    }

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

    public function getMaxNumClients()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_CLIENTS_PRO;
        }

        if ($this->id < LEGACY_CUTOFF) {
            return MAX_NUM_CLIENTS_LEGACY;
        }

        return MAX_NUM_CLIENTS;
    }

    public function getMaxNumVendors()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_VENDORS_PRO;
        }

        return MAX_NUM_VENDORS;
    }

    public function clearSession()
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
     * @return bool
     */
    public function isEmailBeingChanged()
    {
        return Utils::isNinjaProd() && $this->email != $this->getOriginal('email');
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
        }
        if (is_string($permission)) {
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

    public function viewModel($model, $entityType)
    {
        if ($this->hasPermission('view_' . $entityType)) {
            return true;
        }

        return (bool) ($model->user_id == $this->id);
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    public function owns($entity)
    {
        return ! empty($entity->user_id) && $entity->user_id == $this->id;
    }

    /**
     * @return bool|mixed
     */
    public function filterId()
    {   //todo permissions
        return $this->hasPermission('view_all') ? false : $this->id;
    }

    public function filterIdByEntity($entity)
    {
        return $this->hasPermission('view_' . $entity) ? false : $this->id;
    }

    public function caddAddUsers()
    {
        if (! Utils::isNinjaProd()) {
            return true;
        }
        if (! $this->hasFeature(FEATURE_USERS)) {
            return false;
        }

        $account = $this->account;
        $company = $account->company;

        $numUsers = 1;
        foreach ($company->accounts as $account) {
            $numUsers += $account->users->count() - 1;
        }

        return $numUsers < $company->num_users;
    }

    public function canCreateOrEdit($entityType, $entity = false)
    {
        return ($entity && $this->can('edit', $entity))
            || (! $entity && $this->can('create', $entityType));
    }

    public function primaryAccount()
    {
        return $this->account->company->accounts->sortBy('id')->first();
    }

    public function sendPasswordResetNotification($token)
    {
        //$this->notify(new ResetPasswordNotification($token));
        app('App\Ninja\Mailers\UserMailer')->sendPasswordReset($this, $token);
    }

    public function routeNotificationForSlack()
    {
        return $this->slack_webhook_url;
    }

    public function hasAcceptedLatestTerms()
    {
        if (! NINJA_TERMS_VERSION) {
            return true;
        }

        return $this->accepted_terms_version == NINJA_TERMS_VERSION;
    }

    public function acceptLatestTerms($ip)
    {
        $this->accepted_terms_version = NINJA_TERMS_VERSION;
        $this->accepted_terms_timestamp = date('Y-m-d H:i:s');
        $this->accepted_terms_ip = $ip;

        return $this;
    }

    public function ownsEntity($entity)
    {
        return $entity->user_id == $this->id;
    }

    public function shouldNotify($invoice)
    {
        if (! $this->email || ! $this->confirmed) {
            return false;
        }

        if ($this->cannot('view', $invoice)) {
            return false;
        }

        return ! ($this->only_notify_owned && ! $this->ownsEntity($invoice));
    }

    public function permissionsMap()
    {
        $data = [];
        $permissions = json_decode($this->permissions);

        if (! $permissions) {
            return $data;
        }

        $keys = array_values((array) $permissions);
        $values = array_fill(0, count($keys), true);

        return array_combine($keys, $values);
    }

    public function eligibleForMigration()
    {
        return null === $this->public_id || $this->public_id == 0;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

User::created(function ($user) {
    LookupUser::createNew($user->account->account_key, [
        'email'             => $user->email,
        'user_id'           => $user->id,
        'confirmation_code' => $user->confirmation_code,
    ]);
});

User::updating(function ($user) {
    User::onUpdatingUser($user);

    $dirty = $user->getDirty();
    if (
        array_key_exists('email', $dirty)
        || array_key_exists('confirmation_code', $dirty)
        || array_key_exists('oauth_user_id', $dirty)
        || array_key_exists('oauth_provider_id', $dirty)
        || array_key_exists('referral_code', $dirty)
    ) {
        LookupUser::updateUser($user->account->account_key, $user);
    }
});

User::updated(function ($user) {
    User::onUpdatedUser($user);
});

User::deleted(function ($user) {
    if (! $user->email) {
        return;
    }

    if ($user->forceDeleting) {
        LookupUser::deleteWhere([
            'email' => $user->email,
        ]);
    }
});
