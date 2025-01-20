<?php

namespace App\Models;

use App\Libraries\Utils;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountTicketSettings.
 */
class AccountTicketSettings extends Model
{
    public $table = 'company_ticket_settings';

    /**
     * @var array
     */
    protected $guarded = [];

    public static function checkUniqueLocalPart($localPart, company $company): bool
    {
        if (config('ninja.multi_db_enabled')) {
            $result = LookupAccount::where('support_email_local_part', '=', $localPart)
                ->where('account_key', '!=', $company->account_key)->get();
        } else {
            $result = self::where('support_email_local_part', '=', $localPart)
                ->where('company_id', '!=', $company->id)->get();
        }

        if (count($result) == 0) {
            return false;
        }

        return true;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function ticket_master()
    {
        return $this->hasOne(User::class, 'id', 'ticket_master_id');
    }

    public function max_file_sizes()
    {
        $utils = new Utils();

        return $utils->getMaxFileUploadSizes();
    }
}

AccountTicketSettings::updating(function (AccountTicketSettings $companyTicketSettings): void {
    $dirty = $companyTicketSettings->getDirty();
    if (array_key_exists('support_email_local_part', $dirty)) {
        LookupAccount::updateSupportLocalPart($companyTicketSettings->company->account_key, $dirty['support_email_local_part']);
    }
});
