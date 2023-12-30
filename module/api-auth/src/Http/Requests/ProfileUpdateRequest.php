<?php

namespace Cortexitsolution\ApiAuth\Http\Requests;

use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'max:255'],
            'email' => ['sometimes', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['sometimes'],
            'address' => ['sometimes','string'],
            'city' => ['sometimes','string'],
            'state' => ['sometimes','string'],
            'postal_code' => ['sometimes','string'],
            'avater' => ['sometimes','mimes:jpg,png']
        ];
    }
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $this->sendHttpResponseException('validation error.', $validator->errors());
    }
}