<?php

namespace App\Models;

use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use App\Ninja\Presenters\VendorPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Vendor.
 *
 * @property int                            $id
 * @property Carbon|null                    $created_at
 * @property Carbon|null                    $updated_at
 * @property Carbon|null                    $deleted_at
 * @property int                            $user_id
 * @property int                            $account_id
 * @property int|null                       $currency_id
 * @property string|null                    $name
 * @property string                         $address1
 * @property string                         $address2
 * @property string                         $city
 * @property string                         $state
 * @property string                         $postal_code
 * @property int|null                       $country_id
 * @property string                         $work_phone
 * @property string                         $private_notes
 * @property string                         $website
 * @property int                            $is_deleted
 * @property int                            $public_id
 * @property string|null                    $vat_number
 * @property string|null                    $id_number
 * @property string|null                    $transaction_name
 * @property string|null                    $custom_value1
 * @property string|null                    $custom_value2
 * @property Account                        $account
 * @property Country|null                   $country
 * @property Currency|null                  $currency
 * @property Collection<int, Expense>       $expenses
 * @property int|null                       $expenses_count
 * @property Industry|null                  $industry
 * @property Language|null                  $language
 * @property Collection<int, Payment>       $payments
 * @property int|null                       $payments_count
 * @property Size|null                      $size
 * @property User                           $user
 * @property Collection<int, VendorContact> $vendor_contacts
 * @property int|null                       $vendor_contacts_count
 *
 * @method static Builder|Vendor newModelQuery()
 * @method static Builder|Vendor newQuery()
 * @method static Builder|Vendor onlyTrashed()
 * @method static Builder|Vendor query()
 * @method static Builder|Vendor scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Vendor whereAccountId($value)
 * @method static Builder|Vendor whereAddress1($value)
 * @method static Builder|Vendor whereAddress2($value)
 * @method static Builder|Vendor whereCity($value)
 * @method static Builder|Vendor whereCountryId($value)
 * @method static Builder|Vendor whereCreatedAt($value)
 * @method static Builder|Vendor whereCurrencyId($value)
 * @method static Builder|Vendor whereCustomValue1($value)
 * @method static Builder|Vendor whereCustomValue2($value)
 * @method static Builder|Vendor whereDeletedAt($value)
 * @method static Builder|Vendor whereId($value)
 * @method static Builder|Vendor whereIdNumber($value)
 * @method static Builder|Vendor whereIsDeleted($value)
 * @method static Builder|Vendor whereName($value)
 * @method static Builder|Vendor wherePostalCode($value)
 * @method static Builder|Vendor wherePrivateNotes($value)
 * @method static Builder|Vendor wherePublicId($value)
 * @method static Builder|Vendor whereState($value)
 * @method static Builder|Vendor whereTransactionName($value)
 * @method static Builder|Vendor whereUpdatedAt($value)
 * @method static Builder|Vendor whereUserId($value)
 * @method static Builder|Vendor whereVatNumber($value)
 * @method static Builder|Vendor whereWebsite($value)
 * @method static Builder|Vendor whereWorkPhone($value)
 * @method static Builder|Vendor withActiveOrSelected($id = false)
 * @method static Builder|Vendor withArchived()
 * @method static Builder|Vendor withTrashed()
 * @method static Builder|Vendor withoutTrashed()
 *
 * @mixin \Eloquent
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

    protected $casts = ['deleted_at' => 'datetime'];

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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function vendor_contacts()
    {
        return $this->hasMany(VendorContact::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'vendor_id', 'id');
    }

    /**
     * @param      $data
     * @param bool $isPrimary
     *
     * @return Model
     */
    public function addVendorContact($data, $isPrimary = false)
    {
        $publicId = $data['public_id'] ?? ($data['id'] ?? false);

        if ( ! $this->wasRecentlyCreated && $publicId && (int) $publicId > 0) {
            $contact = VendorContact::scope($publicId)->whereVendorId($this->id)->firstOrFail();
        } else {
            $contact = VendorContact::createNew();
        }

        $contact->fill($data);
        $contact->is_primary = $isPrimary;

        return $this->vendor_contacts()->save($contact);
    }

    public function getRoute(): string
    {
        return '/vendors/' . $this->public_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

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
        return $this->hasAddress() && env('GOOGLE_MAPS_ENABLED') !== false;
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
            if ($this->{$field}) {
                return true;
            }
        }

        return false;
    }

    public function getDateCreated()
    {
        if ($this->created_at == '0000-00-00 00:00:00') {
            return '---';
        }

        return $this->created_at->format('m/d/y h:i a');
    }

    public function getCurrencyId()
    {
        if ($this->currency_id) {
            return $this->currency_id;
        }

        if ( ! $this->account) {
            $this->load('account');
        }

        return $this->account->currency_id ?: DEFAULT_CURRENCY;
    }

    /**
     * @return float|int
     */
    public function getTotalExpenses()
    {
        return DB::table('expenses')
            ->select('expense_currency_id', DB::raw('sum(expenses.amount + (expenses.amount * expenses.tax_rate1 / 100) + (expenses.amount * expenses.tax_rate2 / 100)) as amount'))
            ->whereVendorId($this->id)
            ->whereIsDeleted(false)
            ->groupBy('expense_currency_id')
            ->get();
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
