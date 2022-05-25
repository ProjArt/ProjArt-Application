<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
        'title' => 'required|alpha_num',
        'description',
        'start' => 'required|date',
        'end' => 'required|date',
        'location' => 'required|alpha_num',
        'calendar_id' => 'required|numeric'
        ];
    }
}
