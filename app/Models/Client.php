<?php

namespace App\Models;

use App\Models\Traits\HasCustomMessages;
use Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Client.
 *
 * @property int                                                                 $id
 * @property int                                                                 $user_id
 * @property int                                                                 $account_id
 * @property int|null                                                            $currency_id
 * @property \Illuminate\Support\Carbon|null                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                     $updated_at
 * @property \Illuminate\Support\Carbon|null                                     $deleted_at
 * @property string|null                                                         $name
 * @property string|null                                                         $address1
 * @property string|null                                                         $address2
 * @property string|null                                                         $city
 * @property string|null                                                         $state
 * @property string|null                                                         $postal_code
 * @property int|null                                                            $country_id
 * @property string|null                                                         $work_phone
 * @property string|null                                                         $private_notes
 * @property string|null                                                         $balance
 * @property string|null                                                         $paid_to_date
 * @property string|null                                                         $last_login
 * @property string|null                                                         $website
 * @property int|null                                                            $industry_id
 * @property int|null                                                            $size_id
 * @property int                                                                 $is_deleted
 * @property int|null                                                            $payment_terms
 * @property int                                                                 $public_id
 * @property string|null                                                         $custom_value1
 * @property string|null                                                         $custom_value2
 * @property string|null                                                         $vat_number
 * @property string|null                                                         $id_number
 * @property int|null                                                            $language_id
 * @property int|null                                                            $invoice_number_counter
 * @property int|null                                                            $quote_number_counter
 * @property string|null                                                         $public_notes
 * @property int|null                                                            $credit_number_counter
 * @property string                                                              $task_rate
 * @property string|null                                                         $shipping_address1
 * @property string|null                                                         $shipping_address2
 * @property string|null                                                         $shipping_city
 * @property string|null                                                         $shipping_state
 * @property string|null                                                         $shipping_postal_code
 * @property int|null                                                            $shipping_country_id
 * @property int                                                                 $show_tasks_in_portal
 * @property int                                                                 $send_reminders
 * @property mixed|null                                                          $custom_messages
 * @property \App\Models\Account                                                 $account
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property int|null                                                            $activities_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contact>  $contacts
 * @property int|null                                                            $contacts_count
 * @property \App\Models\Country|null                                            $country
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Credit>   $credits
 * @property int|null                                                            $credits_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Credit>   $creditsWithBalance
 * @property int|null                                                            $credits_with_balance_count
 * @property \App\Models\Currency|null                                           $currency
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense>  $expenses
 * @property int|null                                                            $expenses_count
 * @property \App\Models\Industry|null                                           $industry
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice>  $invoices
 * @property int|null                                                            $invoices_count
 * @property \App\Models\Language|null                                           $language
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment>  $payments
 * @property int|null                                                            $payments_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice>  $publicQuotes
 * @property int|null                                                            $public_quotes_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice>  $quotes
 * @property int|null                                                            $quotes_count
 * @property \App\Models\Country|null                                            $shipping_country
 * @property \App\Models\Size|null                                               $size
 * @property \App\Models\User                                                    $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreditNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCustomMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereInvoiceNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePaidToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereQuoteNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSendReminders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShowTasksInPortal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereTaskRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereWorkPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Client withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Client withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Client extends EntityModel
{
    use HasCustomMessages;
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ClientPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'id_number',
        'vat_number',
        'work_phone',
        'custom_value1',
        'custom_value2',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'size_id',
        'industry_id',
        'currency_id',
        'language_id',
        'payment_terms',
        'website',
        'invoice_number_counter',
        'quote_number_counter',
        'public_notes',
        'task_rate',
        'shipping_address1',
        'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country_id',
        'show_tasks_in_portal',
        'send_reminders',
        'custom_messages',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public static function getImportColumns(): array
    {
        return [
            'name',
            'work_phone',
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'public_notes',
            'private_notes',
            'country',
            'website',
            'currency',
            'vat_number',
            'id_number',
            'custom1',
            'custom2',
            'contact_first_name',
            'contact_last_name',
            'contact_phone',
            'contact_email',
            'contact_custom1',
            'contact_custom2',
        ];
    }

    public static function getImportMap(): array
    {
        return [
            'first'                              => 'contact_first_name',
            'last^last4'                         => 'contact_last_name',
            'email'                              => 'contact_email',
            'work|office'                        => 'work_phone',
            'mobile|phone'                       => 'contact_phone',
            'name|organization|description^card' => 'name',
            'apt|street2|address2|line2'         => 'address2',
            'street|address1|line1^avs'          => 'address1',
            'city'                               => 'city',
            'state|province'                     => 'state',
            'zip|postal|code^avs'                => 'postal_code',
            'country'                            => 'country',
            'public'                             => 'public_notes',
            'private|note'                       => 'private_notes',
            'site|website'                       => 'website',
            'currency'                           => 'currency',
            'vat'                                => 'vat_number',
            'number'                             => 'id_number',
        ];
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function quotes()
    {
        return $this->hasMany(\App\Models\Invoice::class)->where('invoice_type_id', '=', INVOICE_TYPE_QUOTE);
    }

    public function publicQuotes()
    {
        return $this->hasMany(\App\Models\Invoice::class)->where('invoice_type_id', '=', INVOICE_TYPE_QUOTE)->whereIsPublic(true);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function shipping_country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }

    public function language()
    {
        return $this->belongsTo(\App\Models\Language::class);
    }

    public function size()
    {
        return $this->belongsTo(\App\Models\Size::class);
    }

    public function industry()
    {
        return $this->belongsTo(\App\Models\Industry::class);
    }

    public function credits()
    {
        return $this->hasMany(\App\Models\Credit::class);
    }

    public function creditsWithBalance()
    {
        return $this->hasMany(\App\Models\Credit::class)->where('balance', '>', 0);
    }

    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class, 'client_id', 'id')->withTrashed();
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\Activity::class, 'client_id', 'id')->orderBy('id', 'desc');
    }

    /**
     * @param      $data
     * @param bool $isPrimary
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addContact($data, $isPrimary = false)
    {
        $publicId = $data['public_id'] ?? ($data['id'] ?? false);

        // check if this client wasRecentlyCreated to ensure a new contact is
        // always created even if the request includes a contact id
        if ( ! $this->wasRecentlyCreated && $publicId && (int) $publicId > 0) {
            $contact = Contact::scope($publicId)->whereClientId($this->id)->firstOrFail();
        } else {
            $contact = Contact::createNew();
            $contact->send_invoice = true;

            if (isset($data['contact_key']) && $this->account->account_key == env('NINJA_LICENSE_ACCOUNT_KEY')) {
                $contact->contact_key = $data['contact_key'];
            } else {
                $contact->contact_key = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
            }
        }

        if ($this->account->isClientPortalPasswordEnabled()) {
            if ( ! empty($data['password']) && $data['password'] != '-%unchanged%-') {
                $contact->password = bcrypt($data['password']);
            } elseif (empty($data['password'])) {
                $contact->password = null;
            }
        }

        $contact->fill($data);
        $contact->is_primary = $isPrimary;
        $contact->email = trim($contact->email);

        return $this->contacts()->save($contact);
    }

    /**
     * @param $balanceAdjustment
     * @param $paidToDateAdjustment
     */
    public function updateBalances($balanceAdjustment, $paidToDateAdjustment): void
    {
        if ($balanceAdjustment == 0 && $paidToDateAdjustment == 0) {
            return;
        }

        $this->balance += $balanceAdjustment;
        $this->paid_to_date += $paidToDateAdjustment;

        $this->save();
    }

    public function getRoute(): string
    {
        return '/clients/' . $this->public_id;
    }

    /**
     * @return float|int
     */
    public function getTotalCredit()
    {
        return \Illuminate\Support\Facades\DB::table('credits')
            ->where('client_id', '=', $this->id)
            ->whereNull('deleted_at')
            ->sum('balance');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrimaryContact()
    {
        if ( ! $this->relationLoaded('contacts')) {
            $this->load('contacts');
        }

        foreach ($this->contacts as $contact) {
            if ($contact->is_primary) {
                return $contact;
            }
        }

        return false;
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }

        if ($contact = $this->getPrimaryContact()) {
            return $contact->getDisplayName();
        }
    }

    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    public function getEntityType(): string
    {
        return ENTITY_CLIENT;
    }

    public function showMap(): bool
    {
        return $this->hasAddress() && env('GOOGLE_MAPS_ENABLED') !== false;
    }

    public function addressesMatch(): bool
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
            if ($this->{$field} != $this->{'shipping_' . $field}) {
                return false;
            }
        }

        return true;
    }

    public function hasAddress($shipping = false): bool
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
            if ($shipping) {
                $field = 'shipping_' . $field;
            }

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

    public function getGatewayToken()
    {
        $accountGateway = $this->account->getGatewayByType(GATEWAY_TYPE_TOKEN);

        if ( ! $accountGateway) {
            return false;
        }

        return AccountGatewayToken::clientAndGateway($this->id, $accountGateway->id)->first();
    }

    public function defaultPaymentMethod()
    {
        if ($token = $this->getGatewayToken()) {
            return $token->default_payment_method;
        }

        return false;
    }

    public function autoBillLater()
    {
        if ($token = $this->getGatewayToken()) {
            if ($this->account->auto_bill_on_due_date) {
                return true;
            }

            return $token->autoBillLater();
        }

        return false;
    }

    public function getAmount(): float|int|array
    {
        return $this->balance + $this->paid_to_date;
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

    public function getCurrencyCode()
    {
        if ($this->currency) {
            return $this->currency->code;
        }

        if ( ! $this->account) {
            $this->load('account');
        }

        return $this->account->currency ? $this->account->currency->code : 'USD';
    }

    public function getCountryCode()
    {
        if ($country = $this->country) {
            return $country->iso_3166_2;
        }

        if ( ! $this->account) {
            $this->load('account');
        }

        return $this->account->country ? $this->account->country->iso_3166_2 : 'US';
    }

    /**
     * @param $isQuote
     *
     * @return mixed
     */
    public function getCounter($isQuote)
    {
        return $isQuote ? $this->quote_number_counter : $this->invoice_number_counter;
    }

    public function markLoggedIn(): void
    {
        $this->last_login = Carbon::now()->toDateTimeString();
        $this->save();
    }

    public function hasAutoBillConfigurableInvoices(): bool
    {
        return $this->invoices()->whereIsPublic(true)->whereIn('auto_bill', [AUTO_BILL_OPT_IN, AUTO_BILL_OPT_OUT])->count() > 0;
    }

    public function hasRecurringInvoices(): bool
    {
        return $this->invoices()->whereIsPublic(true)->whereIsRecurring(true)->count() > 0;
    }

    public function defaultDaysDue()
    {
        return $this->payment_terms == -1 ? 0 : $this->payment_terms;
    }

    public function firstInvitationKey()
    {
        if (($invoice = $this->invoices->first()) && ($invitation = $invoice->invitations->first())) {
            return $invitation->invitation_key;
        }
    }
}

Client::creating(function ($client): void {
    $client->setNullValues();
    $client->account->incrementCounter($client);
});

Client::updating(function ($client): void {
    $client->setNullValues();
});
