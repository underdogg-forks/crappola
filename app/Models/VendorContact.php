<?php

namespace App\Models;

// vendor

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class VendorContact.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int                             $vendor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $is_primary
 * @property string|null                     $first_name
 * @property string|null                     $last_name
 * @property string|null                     $email
 * @property string|null                     $phone
 * @property int|null                        $public_id
 * @property \App\Models\Account             $account
 * @property \App\Models\User                $user
 * @property \App\Models\Vendor              $vendor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact withoutTrashed()
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
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class)->withTrashed();
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

    /**
     * @return string
     */
    public function getFullName(): string
    {
        if ($this->first_name || $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }

        return '';
    }
}
