<?php

namespace App\Models;

// vendor
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class VendorContact.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int         $user_id
 * @property int         $vendor_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int         $is_primary
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property int|null    $public_id
 * @property Account     $account
 * @property User        $user
 * @property Vendor      $vendor
 *
 * @method static Builder|VendorContact newModelQuery()
 * @method static Builder|VendorContact newQuery()
 * @method static Builder|VendorContact onlyTrashed()
 * @method static Builder|VendorContact query()
 * @method static Builder|VendorContact scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|VendorContact whereAccountId($value)
 * @method static Builder|VendorContact whereCreatedAt($value)
 * @method static Builder|VendorContact whereDeletedAt($value)
 * @method static Builder|VendorContact whereEmail($value)
 * @method static Builder|VendorContact whereFirstName($value)
 * @method static Builder|VendorContact whereId($value)
 * @method static Builder|VendorContact whereIsPrimary($value)
 * @method static Builder|VendorContact whereLastName($value)
 * @method static Builder|VendorContact wherePhone($value)
 * @method static Builder|VendorContact wherePublicId($value)
 * @method static Builder|VendorContact whereUpdatedAt($value)
 * @method static Builder|VendorContact whereUserId($value)
 * @method static Builder|VendorContact whereVendorId($value)
 * @method static Builder|VendorContact withActiveOrSelected($id = false)
 * @method static Builder|VendorContact withArchived()
 * @method static Builder|VendorContact withTrashed()
 * @method static Builder|VendorContact withoutTrashed()
 *
 * @mixin \Eloquent
 */
class VendorContact extends EntityModel
{
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

    /**
     * @var string
     */
    protected $table = 'vendor_contacts';

    /**
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'send_invoice',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function getPersonType(): string
    {
        return PERSON_VENDOR_CONTACT;
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

    public function getFullName(): string
    {
        if ($this->first_name || $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }

        return '';
    }
}
