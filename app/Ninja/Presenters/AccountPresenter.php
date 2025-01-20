<?php

namespace App\Ninja\Presenters;

use App\Models\Company;
use App\Models\TaxRate;
use Carbon;
use Domain;
use Laracasts\Presenter\Presenter;
use stdClass;
use App\Libraries\Utils;

/**
 * Class AccountPresenter.
 */
class AccountPresenter extends Presenter
{
    /**
     * @return mixed
     */
    public function name()
    {
        return $this->entity->name ?: trans('texts.untitled_account');
    }

    /**
     * @return string
     */
    public function address()
    {
        $company = $this->entity;

        $str = $company->address1 ?: '';

        if ($company->address2 && $str) {
            $str .= ', ';
        }

        $str .= $company->address2;

        if ($company->getCityState() && $str) {
            $str .= ' - ';
        }

        return $str . $company->getCityState();
    }

    /**
     * @return string
     */
    public function website()
    {
        return Utils::addHttp($this->entity->website);
    }

    /**
     * @return string
     */
    public function taskRate()
    {
        if (floatval($this->entity->task_rate)) {
            return Utils::roundSignificant($this->entity->task_rate);
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function currencyCode()
    {
        $currencyId = $this->entity->getCurrencyId();
        $currency = Utils::getFromCache($currencyId, 'currencies');

        return $currency->code;
    }

    public function clientPortalLink($subdomain = false)
    {
        $company = $this->entity;
        $url = Domain::getLinkFromId($company->domain_id);

        if ($subdomain && $company->subdomain) {
            $url = Utils::replaceSubdomain($url, $company->subdomain);
        }

        return $url;
    }

    public function industry()
    {
        return $this->entity->industry ? $this->entity->industry->name : '';
    }

    public function size()
    {
        return $this->entity->size ? $this->entity->size->name : '';
    }

    public function paymentTerms()
    {
        $terms = $this->entity->payment_terms;

        if ($terms == 0) {
            return '';
        } elseif ($terms == -1) {
            $terms = 0;
        }

        return trans('texts.payment_terms_net') . ' ' . $terms;
    }

    public function dueDatePlaceholder()
    {
        if ($this->entity->payment_terms == 0) {
            return ' ';
        }

        $date = $this->entity->defaultDueDate();

        return $date ? Utils::fromSqlDate($date) : ' ';
    }

    public function rBits()
    {
        $company = $this->entity;
        $user = $company->users()->first();
        $data = [];

        $data[] = $this->createRBit('business_name', 'user', ['business_name' => $company->name]);
        $data[] = $this->createRBit('industry_code', 'user', ['industry_detail' => $company->present()->industry]);
        $data[] = $this->createRBit('comment', 'partner_database', ['comment_text' => 'Logo image not present']);
        $data[] = $this->createRBit('business_description', 'user', ['business_description' => $company->present()->size]);

        $data[] = $this->createRBit('person', 'user', ['name' => $user->getFullName()]);
        $data[] = $this->createRBit('email', 'user', ['email' => $user->email]);
        $data[] = $this->createRBit('phone', 'user', ['phone' => $user->phone]);
        $data[] = $this->createRBit('website_uri', 'user', ['uri' => $company->website]);
        $data[] = $this->createRBit('external_account', 'partner_database', ['is_partner_account' => 'yes', 'account_type' => 'Invoice Ninja', 'create_time' => time()]);

        return $data;
    }

    private function createRBit($type, $source, $properties)
    {
        $data = new stdClass();
        $data->receive_time = time();
        $data->type = $type;
        $data->source = $source;
        $data->properties = new stdClass();

        foreach ($properties as $key => $val) {
            $data->properties->$key = $val;
        }

        return $data;
    }

    public function dateRangeOptions()
    {
        $yearStart = Carbon::parse($this->entity->financialYearStart() ?: date('Y') . '-01-01');
        $month = $yearStart->month - 1;
        $year = $yearStart->year;
        $lastYear = $year - 1;

        $str = '{
            "' . trans('texts.last_7_days') . '": [moment().subtract(6, "days"), moment()],
            "' . trans('texts.last_30_days') . '": [moment().subtract(29, "days"), moment()],
            "' . trans('texts.this_month') . '": [moment().startOf("month"), moment().endOf("month")],
            "' . trans('texts.last_month') . '": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "' . trans('texts.current_quarter') . '": [moment().quarter(chartQuarter).startOf("quarter"), moment().quarter(chartQuarter).endOf("quarter")],
            "' . trans('texts.last_quarter') . '": [moment().subtract(1, "quarter").startOf("quarter"), moment().subtract(1, "quarter").endOf("quarter")],
            "' . trans('texts.this_year') . '": [moment().date(1).month(' . $month . ').year(' . $year . '), moment()],
            "' . trans('texts.last_year') . '": [moment().date(1).month(' . $month . ').year(' . $lastYear . '), moment().date(1).month(' . $month . ').year(' . $year . ').subtract(1, "day")],
        }';

        return $str;
    }

    public function taxRateOptions()
    {
        $rates = TaxRate::scope()->orderBy('name')->get();
        $options = [];

        foreach ($rates as $rate) {
            $name = $rate->name . ' ' . ($rate->rate + 0) . '%';
            if ($rate->is_inclusive) {
                $name .= ' - ' . trans('texts.inclusive');
            }
            $options[($rate->is_inclusive ? '1 ' : '0 ') . $rate->rate . ' ' . $rate->name] = e($name);
        }

        return $options;
    }

    public function customTextFields()
    {
        $fields = [
            'client1' => 'custom_client1',
            'client1' => 'custom_client2',
            'contact1' => 'custom_contact1',
            'contact2' => 'custom_contact2',
            'invoice_text1' => 'custom_invoice1',
            'invoice_text2' => 'custom_invoice2',
            'product1' => 'custom_product1',
            'product2' => 'custom_product2',
        ];
        $data = [];

        foreach ($fields as $key => $val) {
            if ($label = $this->customLabel($key)) {
                $data[Utils::getCustomLabel($label)] = [
                    'value' => $val,
                    'name' => $val,
                ];
            }
        }

        return $data;
    }

    public function customLabel($field)
    {
        return Utils::getCustomLabel($this->entity->customLabel($field));
    }

    public function customDesigns()
    {
        $company = $this->entity;
        $data = [];

        for ($i = 1; $i <= 3; $i++) {
            $label = trans('texts.custom_design' . $i);
            if (!$company->{'custom_design' . $i}) {
                $label .= ' - ' . trans('texts.empty');
            }

            $data[] = [
                'url' => url('/settings/customize_design?design_id=') . ($i + 10),
                'label' => $label,
            ];
        }

        return $data;
    }

    public function clientLoginUrl()
    {
        $company = $this->entity;

        if (Utils::isNinjaProd()) {
            $url = 'https://';
            $url .= $company->subdomain ?: 'app';
            $url .= '.' . Domain::getDomainFromId($company->domain_id);
        } else {
            $url = trim(SITE_URL, '/');
        }

        $url .= '/client/login';

        if (Utils::isNinja()) {
            if (!$company->subdomain) {
                $url .= '?account_key=' . $company->account_key;
            }
        } else {
            if (company::count() > 1) {
                $url .= '?account_key=' . $company->account_key;
            }
        }

        return $url;
    }
}
