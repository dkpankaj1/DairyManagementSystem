<?php

namespace Cortexitsolution\ApiAuth\Http\Requests;

use Cortexitsolution\ApiAuth\Http\Traits\HttpRateLimiter;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;


class LoginRequest extends FormRequest
{
    use HttpResponses,HttpRateLimiter;
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        return $this->sendhttpResponseException('validation error.', $validator->errors());
    }


    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt( ['email' => $this->email,'password' => $this->password,'status' => 1], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            $this->sendhttpResponseException("login errors",['email' => trans('auth.failed')],401);
        }

        RateLimiter::clear($this->throttleKey());

    }

}

?>