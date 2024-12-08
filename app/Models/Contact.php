<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Utils;

/**
 * Class Contact.
 *
 * @property int                                                                                                           $id
 * @property int                                                                                                           $account_id
 * @property int                                                                                                           $user_id
 * @property int                                                                                                           $client_id
 * @property \Illuminate\Support\Carbon|null                                                                               $created_at
 * @property \Illuminate\Support\Carbon|null                                                                               $updated_at
 * @property \Illuminate\Support\Carbon|null                                                                               $deleted_at
 * @property int                                                                                                           $is_primary
 * @property int                                                                                                           $send_invoice
 * @property string|null                                                                                                   $first_name
 * @property string|null                                                                                                   $last_name
 * @property string|null                                                                                                   $email
 * @property string|null                                                                                                   $phone
 * @property string|null                                                                                                   $last_login
 * @property int|null                                                                                                      $public_id
 * @property string|null                                                                                                   $password
 * @property int|null                                                                                                      $confirmation_code
 * @property int|null                                                                                                      $remember_token
 * @property mixed                                                                                                         $contact_key
 * @property string|null                                                                                                   $bot_user_id
 * @property string|null                                                                                                   $custom_value1
 * @property string|null                                                                                                   $custom_value2
 * @property \App\Models\Account|null                                                                                      $account
 * @property \App\Models\Client                                                                                            $client
 * @property string                                                                                                        $link
 * @property \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property int|null                                                                                                      $notifications_count
 * @property \App\Models\User                                                                                              $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereBotUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereContactKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereSendInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Contact extends EntityModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable;
    use CanResetPassword;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var string
     */
    public static $fieldFirstName = 'first_name';

    /**
     * @var string
     */
    public static $fieldLastName = 'last_name';

    /**
     * @var string
     */
    public static $fieldEmail = 'email';

    /**
     * @var string
     */
    public static $fieldPhone = 'phone';

    protected $guard = 'client';

    /**
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'send_invoice',
        'custom_value1',
        'custom_value2',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
        'confirmation_code',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_CONTACT;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    public function getPersonType(): string
    {
        return PERSON_CONTACT;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getDisplayName();
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->getFullName() !== '' && $this->getFullName() !== '0') {
            return $this->getFullName();
        }

        return $this->email;
    }

    /**
     * @return mixed|string
     */
    public function getSearchName()
    {
        $name = $this->getFullName();
        $email = $this->email;

        if ($name && $email) {
            return sprintf('%s <%s>', $name, $email);
        }

        return $name ?: $email;
    }

    /**
     * @param $contact_key
     *
     * @return mixed
     */
    public function getContactKeyAttribute($contact_key)
    {
        if (empty($contact_key) && $this->id) {
            $this->contact_key = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
            $contact_key = $this->contact_key;
            static::where('id', $this->id)->update(['contact_key' => $contact_key]);
        }

        return $contact_key;
    }

    public function getFullName(): string
    {
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return '';
    }

    public function getLinkAttribute(): string
    {
        if ( ! $this->account) {
            $this->load('account');
        }

        $account = $this->account;
        $iframe_url = $account->iframe_url;
        $url = trim(SITE_URL, '/');

        if ($account->hasFeature(FEATURE_CUSTOM_URL)) {
            if (Utils::isNinjaProd() && ! Utils::isReseller()) {
                $url = $account->present()->clientPortalLink();
            }

            if ($iframe_url) {
                if ($account->is_custom_domain) {
                    $url = $iframe_url;
                } else {
                    return sprintf('%s?%s/client', $iframe_url, $this->contact_key);
                }
            } elseif ($this->account->subdomain) {
                $url = Utils::replaceSubdomain($url, $account->subdomain);
            }
        }

        return sprintf('%s/client/dashboard/%s', $url, $this->contact_key);
    }

    public function sendPasswordResetNotification($token): void
    {
        //$this->notify(new ResetPasswordNotification($token));
        app(\App\Ninja\Mailers\ContactMailer::class)->sendPasswordReset($this, $token);
    }
}

Contact::creating(function ($contact): void {
    LookupContact::createNew($contact->account->account_key, [
        'contact_key' => $contact->contact_key,
    ]);
});

Contact::deleted(function ($contact): void {
    if ($contact->forceDeleting) {
        LookupContact::deleteWhere([
            'contact_key' => $contact->contact_key,
        ]);
    }
});
