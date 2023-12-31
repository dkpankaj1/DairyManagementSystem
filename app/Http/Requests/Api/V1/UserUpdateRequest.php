<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\User;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'string'],
            'status' =>  ['sometimes', 'between:0,1'],
            // 'email' => ['sometimes', 'email', Rule::unique(User::class)->ignore($this->user)],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        return $this->sendHttpResponseException(trans('api.validation.error'),$validator->errors());
    }
}
