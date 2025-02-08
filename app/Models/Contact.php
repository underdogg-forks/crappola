<?php

namespace App\Models;

use DateTimeInterface;
use Utils;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

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

    public static function getContactIfLoggedIn()
    {
        if ($contact = self::where('contact_key', '=', session('contact_key'))->with('company')->first()) {
            return $contact;
        }

        return false;
    }

    public static function getContactByContactKey($contact_key)
    {
        if (strlen($contact_key) == 0) {
            return false;
        }

        if ($contact = self::where('contact_key', '=', $contact_key)->first()) {
            return $contact;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CONTACT;
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
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
    public function getPersonType()
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
        if ($this->getFullName()) {
            return $this->getFullName();
        }

        return $this->email;
    }

    public function getFullName(): string
    {
        if ($this->first_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        if ($this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return '';
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
     * @return mixed
     */
    public function getContactKeyAttribute($contact_key)
    {
        if (empty($contact_key) && $this->id) {
            $this->contact_key = $contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            static::where('id', $this->id)->update(['contact_key' => $contact_key]);
        }

        return $contact_key;
    }

    /**
     * @return string
     */
    public function getLinkAttribute()
    {
        if (! $this->company) {
            $this->load('company');
        }

        $company = $this->company;
        $iframe_url = $company->iframe_url;
        $url = trim(SITE_URL, '/');

        if ($company->hasFeature(FEATURE_CUSTOM_URL)) {
            if (Utils::isNinjaProd() && ! Utils::isReseller()) {
                $url = $company->present()->clientPortalLink();
            }

            if ($iframe_url) {
                if ($company->is_custom_domain) {
                    $url = $iframe_url;
                } else {
                    return "{$iframe_url}?{$this->contact_key}/client";
                }
            } elseif ($this->company->subdomain) {
                $url = Utils::replaceSubdomain($url, $company->subdomain);
            }
        }

        return "{$url}/client/dashboard/{$this->contact_key}";
    }

    public function sendPasswordResetNotification($token): void
    {
        //$this->notify(new ResetPasswordNotification($token));
        app(ContactMailer::class)->sendPasswordReset($this, $token);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Contact::creating(function ($contact): void {
    LookupContact::createNew($contact->company->account_key, [
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
