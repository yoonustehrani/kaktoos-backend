<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date|date_format:Y-m-d|after:today',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date',
            'rooms' => 'required|array',
            'rooms.*' => 'required',
            'rooms.*.adults' => 'required|integer|min:1',
            'rooms.*.children' => 'integer|min:0',
            'rooms.*.children_age' => 'array',
            'rooms.*.children_age.*' => 'integer|min:1'
        ];
    }
}
