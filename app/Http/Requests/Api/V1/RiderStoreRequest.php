<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RiderStoreRequest extends FormRequest
{
    use HttpResponses;
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
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', 'numeric'],
            'status' => ['required', 'between:0,1'],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        return $this->sendhttpResponseException(trans('api.validation.error'), $validator->errors(), 406);
    }
    public function generatePassword()
    {
        return Str::random(8);
    }
}
