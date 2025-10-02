<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateComplexTripRequest extends FormRequest
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
            'starts_at' => 'required|date_format:Y-m-d H:i',
            'capacity' => 'required|integer',
            'min_age' => 'required|integer',
            'price' => 'required|integer',
            'start_location' => 'required|string',
            'duration' => 'required|integer',
            'points' => 'required|array',
            'points.*.file_name' => 'required|string',
            'points.*.description' => 'required|string',
            'points.*.day_of_the_route' => 'required|integer',
            'points.*.name' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->error($validator->errors(), "Validation error", 422)
        );
    }
}
