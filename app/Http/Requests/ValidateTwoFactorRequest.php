<?php

namespace App\Http\Requests;

use App\Models\User;
use Exception;
use Google2FA;
use Illuminate\Validation\Factory as ValidationFactory;

class ValidateTwoFactorRequest extends Request
{
    /**
     * @var User
     */
    private $user;

    public function __construct(ValidationFactory $factory)
    {
        $factory->extend(
            'valid_token',
            function ($attribute, $value, $parameters, $validator) {
                $secret = \Illuminate\Support\Facades\Crypt::decrypt($this->user->google_2fa_secret);

                return Google2FA::verifyKey($secret, $value);
            },
            trans('texts.invalid_code')
        );

        $factory->extend(
            'used_token',
            function ($attribute, string $value, $parameters, $validator): bool {
                $key = $this->user->id . ':' . $value;

                return ! \Illuminate\Support\Facades\Cache::has($key);
            },
            trans('texts.invalid_code')
        );
    }

    public function authorize(): bool
    {
        try {
            $this->user = User::findOrFail(
                session('2fa:user:id')
            );
        } catch (Exception) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'totp' => 'bail|required|digits:6|valid_token|used_token',
        ];
    }
}
