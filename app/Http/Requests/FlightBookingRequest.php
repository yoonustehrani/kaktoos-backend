<?php

namespace App\Http\Requests;

use App\Models\Passenger;
use App\Parto\Domains\Flight\Enums\PartoPassengerGender;
use App\Parto\Domains\Flight\Enums\PartoPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\Enums\TravellerTitle;
use App\Parto\Domains\Flight\PricedItinerary;
use App\Parto\Facades\Parto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class FlightBookingRequest extends FormRequest
{
    public PricedItinerary $revalidated_flight;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->validate([
            'ref' => 'required|string|min:20'
        ]);
        $revalidated = Parto::api()->air()->revalidate($this->input('ref'))?->PricedItinerary;
        abort_if(! $revalidated, 404, __('Couldn\'t find the flight'));
        $this->revalidated_flight = new PricedItinerary($revalidated);
        
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $initial_rules = $this->getReserveInitialRules();
        $normal_rules = [
            'passengers.*.national_id' => Rule::forEach(function ($_v, $_a, $_p, array $this_passenger) {
                return [
                    Rule::requiredIf(fn() => $this_passenger['nationality'] == 'IR'),
                    Rule::excludeIf(fn() => $this_passenger['nationality'] != 'IR'),
                    'string',
                    'numeric',
                    'digits:10'
                ];
            }),
            'passengers.*.passport' => ['required_without:passengers.*.national_id', 'array'],
        ];
        $passport_required = [
            'passengers.*.passport' => ['required', 'array'],
        ];
        $initial_rules = $this->input('is_international') 
                ? $initial_rules->merge($passport_required)
                : $initial_rules->merge($normal_rules);
        return $initial_rules->merge($this->getPassportDetailsRules())->toArray();
    }

    /**
     * Get the "after" validation callables for the request.
     * @return Callable[]
     */
    public function after(): array
    {
        return [
            function(Validator $validator) {
                if ($validator->errors()->count() > 0) {
                    return;
                }
                foreach ($this->input('passengers') as $key => $passenger) {
                    $birthdate = Carbon::createFromFormat('Y-m-d', $passenger['birthdate']);
                    $years_old = $birthdate->diff($this->revalidated_flight->getLastFlightSegmentTime())->y;
                    if ($years_old >= 12) {
                        $accepted_type = TravellerPassengerType::Adt;
                    } else if ($years_old < 2) {
                        $accepted_type = TravellerPassengerType::Inf;
                    } else {
                        $accepted_type = TravellerPassengerType::Chd;
                    }
                    $validator->errors()->addIf(
                        boolean: TravellerPassengerType::tryFrom($passenger['type']) !== $accepted_type,
                        key: "passengers.$key.type",
                        message: __('validation.passenger_type_check', [
                            'age' => $years_old,
                            'type' => __($accepted_type->value),
                            'other_type' => __($passenger['type'])
                        ])
                    );
                }
            }
        ];
    }
    protected function getReserveInitialRules()
    {
        return collect([
            'ref' => 'bail|required|string|min:10|numeric',
            'is_international' => 'boolean|required',
            'passengers' => [ 'required', 'array', 'min:1', 'max:9' ],
            'passengers.*' => [ 'required', 'array' ],
            'passengers.*.birthdate' => 'required|date|date_format:Y-m-d|before:today',
            'passengers.*.gender' => ['required', Rule::enum(TravellerGender::class)],
            'passengers.*.type' => ['required', Rule::enum(TravellerPassengerType::class)],
            'passengers.*.first_name' => 'required|string|min:3|max:25',
            // 'passengers.*.middle_name' => ['required_unless:passengers.*.nationality,IR', 'string', 'min:1', 'max:25'],
            'passengers.*.last_name' => 'required|string|min:3|max:25',
            'passengers.*.wheelchair' => ['required', 'boolean'],
            'passengers.*.seat_type' => ['nullable', Rule::enum(TravellerSeatPreference::class)],
            'passengers.*.nationality' => 'required|string|size:2|alpha|regex:/[A-Z]{2}/',
        ]);
    }

    protected function getPassportDetailsRules()
    {
        return collect([
            'passengers.*.passport.country' => 'required_with:passengers.*.passport|string|size:2|alpha|regex:/[A-Z]{2}/',
            'passengers.*.passport.passport_number' => 'required_with:passengers.*.passport|string|alpha_num',
            'passengers.*.passport.expiry_date' => [
                'required_with:passengers.*.passport',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:' . $this->revalidated_flight->getLastFlightSegmentTime()->addUTCMonths(6)->format('Y-m-d'),
            ],
            'passengers.*.passport.issue_date' => [
                $this->revalidated_flight->isPassportIssueDateMandatory() ? 'required' : 'nullable',
                'date',
                'date_format:Y-m-d',
                'before:tomorrow'
            ]
        ]);
    }

    public function getPassengersAsPassenger()
    {
        return array_map(function(array $passenger) {
            $type = TravellerPassengerType::tryFrom($passenger['type']);
            $gender = TravellerGender::tryFrom($passenger['gender']);
            switch ($type) {
                case TravellerPassengerType::Adt:
                    $title = $gender == TravellerGender::Male ? TravellerTitle::Mr : TravellerTitle::Ms;
                    break;
                default:
                    $title = $gender == TravellerGender::Male ? TravellerTitle::Mstr : TravellerTitle::Miss;
                    break;
            }
            return new Passenger([
                'gender' => $gender->value,
                'type' => $type->value,
                'title' => $title->name,
                'first_name' => str($passenger['first_name'])->upper(),
                'middle_name' => isset($passenger['middle_name']) ? str($passenger['middle_name'])->upper() : null,
                'last_name' => str($passenger['last_name'])->upper(),
                'birthdate' => $passenger['birthdate'],
                'country_code' => $passenger['nationality'],
                'national_id' => $passenger['national_id'] ?? null,
                'passport_number' => $passenger['passport']['passport_number'] ?? null,
                'passport_expires_on' => $passenger['passport']['expiry_date'] ?? null,
                'passport_issued_on' => $passenger['passport']['issue_date'] ?? null,
                'passport_country' => $passenger['passport']['country'] ?? null
            ]);
        }, $this->input('passengers'));
    }
}
