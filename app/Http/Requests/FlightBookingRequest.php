<?php

namespace App\Http\Requests;

use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class FlightPricesRequest extends FormRequest
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
            'ref' => 'required|string|min:10|numeric',
            'passengers' => ['required', 'array'],
            'passengers.*.birthdate' => 'required|date|date_format:Y-m-d|before:today',
            'passengers.*.gender' => ['required', Rule::enum(TravellerGender::class)],
            'passengers.*.type' => ['required', Rule::enum(TravellerPassengerType::class)],
            'passengers.*.first_name' => 'required|string|min:3|max:25',
            'passengers.*.middle_name' => ['required_unless:passengers.*.nationality,IR', 'string', 'min:1', 'max:25'],
            'passengers.*.last_name' => 'required|string|min:3|max:25',
            'passengers.*.wheelchair' => ['required', 'boolean'],
            'passengers.*.nationality' => 'required|string|size:2|alpha|regex:/[A-Z]{2}/',
            'passengers.*.national_id' => 'required_without:passport|string|numeric|size:10',
            'passengers.*.passport' => ['required_without:passengers.*.national_id', 'array'],
            'passengers.*.passport.*.country' => 'required|string|size:2|alpha|regex:/[A-Z]{2}/',
            'passengers.*.passport.*.passport_number' => 'required|string|alpha_num',
            'passengers.*.passport.*.expiry_date' => 'required|date|date_format:Y-m-d',
            'passengers.*.passport.*.issue_date' => 'nullable|date|date_format:Y-m-d'
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     * @return Callable[]
     */
    public function after(): array
    {
        return [
            function(Validator $validator) {
                foreach ($this->input('passengers') as $key => $passenger) {
                    $birthdate = Carbon::createFromFormat('Y-m-d', $passenger['birthdate']);
                    // TODO now() is not acceptable it should be a flight date
                    $years_old = $birthdate->diff(now())->y;
                    if ($years_old >= 12) {
                        $accepted_type = TravellerPassengerType::Adt;
                    } else if ($years_old < 2) {
                        $accepted_type = TravellerPassengerType::Inf;
                    } else {
                        $accepted_type = TravellerPassengerType::Chd;
                    }
                    $validator->errors()->addIf(
                        boolean: TravellerPassengerType::tryFrom($passenger['type']) === $accepted_type,
                        key: "passengers.$key.birthdate",
                        message: __('validation.passenger_type_check', [
                            'age' => $years_old,
                            'type' => __('adult'),
                            'other_type' => __('child')
                        ])
                    );
                }
            }
        ];
    }
}
