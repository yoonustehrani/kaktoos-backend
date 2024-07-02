<?php

namespace App\Http\Requests;

use App\Parto\Domains\Flight\Enums\FlightCabinType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FlightSearchRequest extends FormRequest
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
            'origin' => 'string|required|regex:/[a-zA-Z]{1,}:[A-Z]{3}/',
            'destination' => 'string|required|regex:/[a-zA-Z]{1,}:[A-Z]{3}/',
            'date' => 'required|string|date|after:yesterday|date_format:Y-m-d',
            'return_date' => [
                Rule::requiredIf(fn() => str_contains($this->getPathInfo(), "roundtrip")),
                'string',
                'date',
                'after:date',
                'date_format:Y-m-d',
            ],
            'cabin_type' => [
                'nullable',
                Rule::enum(FlightCabinType::class)
            ],
            'passengers.adults' => 'required|integer|min:1|max:6',
            'passengers.children' => 'nullable|integer|min:0', // TODO: custom flight rule
            'passengers.infants' => 'nullable|integer|min:0' // TODO: custom flight rule
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
                if ($validator->errors()->count() > 0) {
                    return;
                }
                switch (true) {
                    case $this->has('passengers.children') && $this->has('passengers.infants'):
                        $break = false;
                        if ($this->input('passengers.infants') > $this->input('passengers.adults')) {
                            $break = true;
                            $validator->errors()->add(
                                'passengers.infants',
                                __('validation.max.numeric', [
                                    'attribute' => __('validation.attributes.passengers.infants'),
                                    'min' => 0,
                                    'max' => $this->input('passengers.adults')
                                ])
                            );
                        }
                        if ($this->input('passengers.children') / 2  > $this->input('passengers.adults')) {
                            $break = true;
                            $validator->errors()->add(
                                'passengers.children',
                                __('validation.max.numeric', [
                                    'attribute' => __('validation.attributes.passengers.children'),
                                    'min' => 0,
                                    'max' => $this->input('passengers.adults') * 2
                                ])
                            );
                        }
                        if($break) break;
                    case $this->has('passengers.children'):
                        if ($this->input('passengers.children') / $this->input('passengers.adults') > 3) {
                            $validator->errors()->add(
                                'passengers.children',
                                __('validation.max.numeric', [
                                    'attribute' => __('validation.attributes.passengers.children'),
                                    'min' => 0,
                                    'max' => $this->input('passengers.adults') * 3
                                ])
                            );
                        }
                    case $this->has('passengers.infants'):
                        if ($this->input('passengers.infants') > $this->input('passengers.adults')) {
                            $validator->errors()->add(
                                'passengers.infants',
                                __('validation.max.numeric', [
                                    'attribute' => __('validation.attributes.passengers.infants'),
                                    'min' => 0,
                                    'max' => $this->input('passengers.adults')
                                ])
                            );
                        }
                    default:
                        # code...
                        break;
                }
            }
        ];
    }
}
