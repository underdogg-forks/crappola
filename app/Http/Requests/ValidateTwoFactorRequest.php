<?php

namespace App\Http\Requests;

use App\Models\User;
use Google2FA;
use Illuminate\Validation\Factory as ValidatonFactory;

class ValidateTwoFactorRequest extends Request
{
    /**
     * @var \App\User
     */
    private $user;

    /**
     * Create a new FormRequest instance.
     *
     * @param \Illuminate\Validation\Factory $factory
     *
     * @return void
     */
    public function __construct(ValidatonFactory $factory)
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

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'totp' => 'bail|required|digits:6|valid_token|used_token',
        ];
    }
}
