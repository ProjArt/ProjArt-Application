<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShareCalendarRequest extends FormRequest
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
            'calendar_id' => 'required|exists:calendars,id',
            'users' => 'required|array',
            'users.*' => 'required|exists:users,username',
            "can_own" => "required|boolean",
        ];
    }
}
