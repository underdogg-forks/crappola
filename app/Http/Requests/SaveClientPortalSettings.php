<?php

namespace App\Http\Requests;

use App\Libraries\Utils;
use HTMLUtils;

class SaveClientPortalSettings extends Request
{
    public function authorize()
    {
        return $this->user()->is_admin && $this->user()->isPro();
    }

    public function rules()
    {
        $rules = [];

        if ($this->custom_link == 'subdomain' && Utils::isNinja()) {
            $rules['subdomain'] = "unique:accounts,subdomain,{$this->user()->account_id},id|valid_subdomain";
        }

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if ($this->client_view_css && Utils::isNinja()) {
            $input['client_view_css'] = HTMLUtils::sanitizeCSS($this->client_view_css);
        }

        if (Utils::isNinja()) {
            if ($this->custom_link == 'subdomain') {
                $subdomain = mb_substr(mb_strtolower($input['subdomain']), 0, MAX_SUBDOMAIN_LENGTH);
                $input['subdomain'] = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $subdomain);
                $input['iframe_url'] = null;
            } else {
                $iframeURL = mb_substr(mb_strtolower($input['iframe_url']), 0, MAX_IFRAME_URL_LENGTH);
                $iframeURL = preg_replace('/[^a-zA-Z0-9_\-\:\/\.]/', '', $iframeURL);
                $input['iframe_url'] = $iframeURL;
                $input['subdomain'] = null;
            }
        }

        $this->replace($input);

        return $this->all();
    }
}
