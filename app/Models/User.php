<?php

namespace App\Models;

use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Libraries\Utils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class User.
 *
 * @property int                                                                                                           $id
 * @property int                                                                                                           $account_id
 * @property \Illuminate\Support\Carbon|null                                                                               $created_at
 * @property \Illuminate\Support\Carbon|null                                                                               $updated_at
 * @property \Illuminate\Support\Carbon|null                                                                               $deleted_at
 * @property string|null                                                                                                   $first_name
 * @property string|null                                                                                                   $last_name
 * @property string|null                                                                                                   $phone
 * @property string                                                                                                        $username
 * @property string|null                                                                                                   $email
 * @property string                                                                                                        $password
 * @property string|null                                                                                                   $confirmation_code
 * @property int                                                                                                           $registered
 * @property int                                                                                                           $confirmed
 * @property int                                                                                                           $notify_sent
 * @property int                                                                                                           $notify_viewed
 * @property int                                                                                                           $notify_paid
 * @property int|null                                                                                                      $public_id
 * @property int                                                                                                           $force_pdfjs
 * @property string|null                                                                                                   $remember_token
 * @property int|null                                                                                                      $news_feed_id
 * @property int                                                                                                           $notify_approved
 * @property int|null                                                                                                      $failed_logins
 * @property int|null                                                                                                      $dark_mode
 * @property string|null                                                                                                   $referral_code
 * @property string|null                                                                                                   $oauth_user_id
 * @property int|null                                                                                                      $oauth_provider_id
 * @property int                                                                                                           $is_admin
 * @property string|null                                                                                                   $bot_user_id
 * @property string|null                                                                                                   $google_2fa_secret
 * @property string|null                                                                                                   $remember_2fa_token
 * @property string|null                                                                                                   $slack_webhook_url
 * @property string|null                                                                                                   $accepted_terms_version
 * @property string|null                                                                                                   $accepted_terms_timestamp
 * @property string|null                                                                                                   $accepted_terms_ip
 * @property int|null                                                                                                      $only_notify_owned
 * @property string                                                                                                        $permissions
 * @property \App\Models\Account                                                                                           $account
 * @property \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property int|null                                                                                                      $notifications_count
 * @property \App\Models\Theme|null                                                                                        $theme
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptedTermsIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptedTermsTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptedTermsVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBotUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFailedLogins($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForcePdfjs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogle2faSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNewsFeedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifySent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifyViewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOauthProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOauthUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOnlyNotifyOwned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRemember2faToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSlackWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var array
     */
    public static $all_permissions = [
        'create_all' => 0b0001,
        'view_all'   => 0b0010,
        'edit_all'   => 0b0100,
    ];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\UserPresenter::class;

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

    protected $casts = ['deleted_at' => 'datetime'];

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
            $user->confirmation_code = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
        }
    }

    /**
     * @param $user
     */
    public static function onUpdatedUser($user): void
    {
        if ( ! $user->getOriginal('email')
            || $user->getOriginal('email') == TEST_USERNAME
            || $user->getOriginal('username') == TEST_USERNAME
            || $user->getOriginal('email') == 'tests@bitrock.com') {
            event(new UserSignedUp());
        }

        event(new UserSettingsChanged($user));
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function theme()
    {
        return $this->belongsTo(\App\Models\Theme::class);
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
    public function getName()
    {
        return $this->getDisplayName();
    }

    public function getPersonType(): string
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

    public function isTrusted(): bool
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

    public function maxInvoiceDesignId(): int
    {
        return $this->hasFeature(FEATURE_MORE_INVOICE_DESIGNS) ? 13 : COUNT_FREE_DESIGNS;
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->getFullName() !== '' && $this->getFullName() !== '0') {
            return $this->getFullName();
        }

        if ($this->email) {
            return $this->email;
        }

        return trans('texts.guest');
    }

    public function getFullName(): string
    {
        if ($this->first_name || $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }

        return '';
    }

    public function showGreyBackground(): bool
    {
        return ! $this->theme_id || in_array($this->theme_id, [2, 3, 5, 6, 7, 8, 10, 11, 12]);
    }

    public function getRequestsCount()
    {
        return \Illuminate\Support\Facades\Session::get(SESSION_COUNTER, 0);
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

    public function getMaxNumClients(): int
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_CLIENTS_PRO;
        }

        if ($this->id < LEGACY_CUTOFF) {
            return MAX_NUM_CLIENTS_LEGACY;
        }

        return MAX_NUM_CLIENTS;
    }

    public function getMaxNumVendors(): int
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
            \Illuminate\Support\Facades\Session::forget($key);
        }
    }

    public function isEmailBeingChanged(): bool
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
                return count(array_intersect($permission, json_decode($this->permissions, 1))) === count($permission);
            }

            return array_intersect($permission, json_decode($this->permissions, 1)) !== [];
        }

        return false;
    }

    public function viewModel($model, string $entityType)
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
    public function owns($entity): bool
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

    public function filterIdByEntity(string $entity)
    {
        return $this->hasPermission('view_' . $entity) ? false : $this->id;
    }

    public function caddAddUsers()
    {
        if ( ! Utils::isNinjaProd()) {
            return true;
        }

        if ( ! $this->hasFeature(FEATURE_USERS)) {
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
        if ($entity && $this->can('edit', $entity)) {
            return true;
        }

        return ! $entity && $this->can('create', $entityType);
    }

    public function primaryAccount()
    {
        return $this->account->company->accounts->sortBy('id')->first();
    }

    public function sendPasswordResetNotification($token): void
    {
        //$this->notify(new ResetPasswordNotification($token));
        app(\App\Ninja\Mailers\UserMailer::class)->sendPasswordReset($this, $token);
    }

    public function routeNotificationForSlack()
    {
        return $this->slack_webhook_url;
    }

    public function hasAcceptedLatestTerms()
    {
        if (NINJA_TERMS_VERSION === '') {
            return true;
        }

        return $this->accepted_terms_version == NINJA_TERMS_VERSION;
    }

    public function acceptLatestTerms($ip): static
    {
        $this->accepted_terms_version = NINJA_TERMS_VERSION;
        $this->accepted_terms_timestamp = date('Y-m-d H:i:s');
        $this->accepted_terms_ip = $ip;

        return $this;
    }

    public function ownsEntity($entity): bool
    {
        return $entity->user_id == $this->id;
    }

    public function shouldNotify($invoice)
    {
        if ( ! $this->email || ! $this->confirmed) {
            return false;
        }

        if ($this->cannot('view', $invoice)) {
            return false;
        }

        return ! ($this->only_notify_owned && ! $this->ownsEntity($invoice));
    }

    public function permissionsMap(): array
    {
        $data = [];
        $permissions = json_decode($this->permissions);

        if ( ! $permissions) {
            return $data;
        }

        $keys = array_values((array) $permissions);
        $values = array_fill(0, count($keys), true);

        return array_combine($keys, $values);
    }

    public function eligibleForMigration(): bool
    {
        return null === $this->public_id || $this->public_id == 0;
    }
}

User::created(function ($user): void {
    LookupUser::createNew($user->account->account_key, [
        'email'             => $user->email,
        'user_id'           => $user->id,
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
        LookupUser::updateUser($user->account->account_key, $user);
    }
});

User::updated(function ($user): void {
    User::onUpdatedUser($user);
});

User::deleted(function ($user): void {
    if ( ! $user->email) {
        return;
    }

    if ($user->forceDeleting) {
        LookupUser::deleteWhere([
            'email' => $user->email,
        ]);
    }
});
