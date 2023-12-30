<?php

namespace Cortexitsolution\ApiAuth\Http\Requests;

use Cortexitsolution\ApiAuth\Http\Traits\HttpRateLimiter;
use Illuminate\Foundation\Http\FormRequest;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;

class PasswordResetRequest extends FormRequest
{
    use HttpResponses,HttpRateLimiter;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email','exists:users'],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $this->sendhttpResponseException('validation error.', $validator->errors());
    }

}