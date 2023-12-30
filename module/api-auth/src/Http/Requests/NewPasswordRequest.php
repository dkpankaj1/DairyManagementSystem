<?php

namespace Cortexitsolution\ApiAuth\Http\Requests;


use Cortexitsolution\ApiAuth\Http\Traits\HttpRateLimiter;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class NewPasswordRequest extends FormRequest
{
    use HttpResponses,HttpRateLimiter;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'otp' => ['required'],
            'email' => ['required', 'email','exists:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->sendHttpResponseException('validation error.', $validator->errors());
    }
}