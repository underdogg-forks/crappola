<?php

namespace App\Models;

use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use DateTimeInterface;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Vendor.
 */
class Vendor extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    public static $fieldName = 'name';

    /**
     * @var string
     */
    public static $fieldPhone = 'work_phone';

    /**
     * @var string
     */
    public static $fieldAddress1 = 'address1';

    /**
     * @var string
     */
    public static $fieldAddress2 = 'address2';

    /**
     * @var string
     */
    public static $fieldCity = 'city';

    /**
     * @var string
     */
    public static $fieldState = 'state';

    /**
     * @var string
     */
    public static $fieldPostalCode = 'postal_code';

    /**
     * @var string
     */
    public static $fieldNotes = 'notes';

    /**
     * @var string
     */
    public static $fieldCountry = 'country';

    /**
     * @var string
     */
    protected $presenter = VendorPresenter::class;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'id_number',
        'vat_number',
        'work_phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'currency_id',
        'website',
        'transaction_name',
        'custom_value1',
        'custom_value2',
    ];

    public static function getImportColumns(): array
    {
        return [
            self::$fieldName,
            self::$fieldPhone,
            self::$fieldAddress1,
            self::$fieldAddress2,
            self::$fieldCity,
            self::$fieldState,
            self::$fieldPostalCode,
            self::$fieldCountry,
            self::$fieldNotes,
            'contact_first_name',
            'contact_last_name',
            'contact_email',
            'contact_phone',
        ];
    }

    public static function getImportMap(): array
    {
        return [
            'first'                    => 'contact_first_name',
            'last'                     => 'contact_last_name',
            'email'                    => 'contact_email',
            'mobile|phone'             => 'contact_phone',
            'work|office'              => 'work_phone',
            'name|organization|vendor' => 'name',
            'street2|address2'         => 'address2',
            'street|address|address1'  => 'address1',
            'city'                     => 'city',
            'state|province'           => 'state',
            'zip|postal|code'          => 'postal_code',
            'country'                  => 'country',
            'note'                     => 'notes',
        ];
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
     * @return HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @return BelongsTo
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * @return BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * @return HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'vendor_id', 'id');
    }

    /**
     * @param bool $isPrimary
     *
     * @return Model
     */
    public function addVendorContact($data, $isPrimary = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : (isset($data['id']) ? $data['id'] : false);

        if (! $this->wasRecentlyCreated && $publicId && intval($publicId) > 0) {
            $contact = VendorContact::scope($publicId)->whereVendorId($this->id)->firstOrFail();
        } else {
            $contact = VendorContact::createNew();
        }

        $contact->fill($data);
        $contact->is_primary = $isPrimary;

        return $this->vendor_contacts()->save($contact);
    }

    /**
     * @return HasMany
     */
    public function vendor_contacts()
    {
        return $this->hasMany(VendorContact::class);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/vendors/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    public function getEntityType(): string
    {
        return 'vendor';
    }

    public function showMap(): bool
    {
        if (! $this->hasAddress()) {
            return false;
        }

        return env('GOOGLE_MAPS_ENABLED') !== false;
    }

    public function hasAddress(): bool
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        foreach ($fields as $field) {
            if ($this->$field) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getDateCreated()
    {
        if ($this->created_at == '0000-00-00 00:00:00') {
            return '---';
        }

        return $this->created_at->format('m/d/y h:i a');
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        if ($this->currency_id) {
            return $this->currency_id;
        }

        if (! $this->company) {
            $this->load('company');
        }

        return $this->company->currency_id ?: DEFAULT_CURRENCY;
    }

    /**
     * @return float|int
     */
    public function getUnpaidExpenses()
    {
        return DB::table('expenses')
            ->select('invoice_currency_id', DB::raw('sum(expenses.amount + (expenses.amount * expenses.tax_rate1 / 100) + (expenses.amount * expenses.tax_rate2 / 100)) as amount'))
            ->whereVendorId($this->id)
            ->whereIsDeleted(false)
            ->whereNull('payment_date')
            ->groupBy('invoice_currency_id')
            ->get();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Vendor::creating(function ($vendor): void {
    $vendor->setNullValues();
});

Vendor::created(function ($vendor): void {
    event(new VendorWasCreated($vendor));
});

Vendor::updating(function ($vendor): void {
    $vendor->setNullValues();
});

Vendor::updated(function ($vendor): void {
    event(new VendorWasUpdated($vendor));
});

Vendor::deleting(function ($vendor): void {
    $vendor->setNullValues();
});

Vendor::deleted(function ($vendor): void {
    event(new VendorWasDeleted($vendor));
});
