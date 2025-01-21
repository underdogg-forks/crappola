<?php

namespace App\Ninja\OAuth;

use App\Models\LookupUser;
use App\Models\User;
use App\Ninja\OAuth\Providers\Google;

class OAuth
{
    public const SOCIAL_GOOGLE = 1;

    public const SOCIAL_FACEBOOK = 2;

    public const SOCIAL_GITHUB = 3;

    public const SOCIAL_LINKEDIN = 4;

    private ?Google $providerInstance = null;

    private ?int $providerId = null;

    public function __construct() {}

    public function getProvider($provider)
    {
        switch ($provider) {
            case 'google':
                $this->providerInstance = new Google();
                $this->providerId = self::SOCIAL_GOOGLE;

                return $this;

            default:
                return;
                break;
        }
    }

    public function getTokenResponse($token)
    {
        $user = null;

        $payload = $this->providerInstance->getTokenResponse($token);
        $oauthUserId = $this->providerInstance->harvestSubField($payload);

        LookupUser::setServerByField('oauth_user_key', $this->providerId . '-' . $oauthUserId);

        if ($this->providerInstance instanceof Google) {
            $user = User::where('oauth_user_id', $oauthUserId)->where('oauth_provider_id', $this->providerId)->first();
        }

        if ($user) {
            return $user;
        }

        return false;
    }
}
