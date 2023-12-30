<?php

namespace Cortexitsolution\ApiAuth\Http\Requests;

use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class PasswordUpdateRequest extends FormRequest
{
    use HttpResponses;
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $this->sendhttpResponseException('validation error.', $validator->errors());
    }

    public function validateOldPassword()
    {
        return Hash::check($this->old_password, $this->user()->password);
    }
}