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
    protected $dates = ['deleted_at'];

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

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_CONTACT;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    /**
     * @return mixed
     */
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

    /**
     * @return string
     */
    public function getFullName(): string
    {
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return '';
    }

    /**
     * @return string
     */
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
