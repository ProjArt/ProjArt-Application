<?php

namespace App\Http\Requests;

use App\Rules\LowerCaseRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthUserRequest extends FormRequest
{
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
            'username' => ['required', 'string', 'max:255', new LowerCaseRule()],
            'password' => 'required|min:5',
            'classroom_name' => 'string|max:255|exists:classrooms,name',
        ];
    }
}
