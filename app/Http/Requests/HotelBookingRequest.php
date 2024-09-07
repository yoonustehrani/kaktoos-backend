<?php

namespace App\Http\Requests;

use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HotelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'ref' => 'required|string',
            'rooms' => 'required|array',
            'rooms.*.guests' => 'required|array',
            'rooms.*.guests.*.first_name' => 'required|string|min:2|max:40',
            'rooms.*.guests.*.last_name' => 'required|string|min:2|max:60',
            'rooms.*.guests.*.type' => ['required', Rule::enum(TravellerPassengerType::class)],
            'rooms.*.guests.*.age' => ['required_if:rooms.*.guests.*.type,' . TravellerPassengerType::Chd->value],
            'rooms.*.guests.*.gender' => ['required', Rule::enum(TravellerGender::class)],
            'rooms.*.guests.*.national_id' => ['required_without:rooms.*.guests.*.passport_number', 'string', 'numeric', 'digits:10'],
            'rooms.*.guests.*.passport_number' => ['required_without:rooms.*.guests.*.national_id', 'string'],
        ];
    }
}
