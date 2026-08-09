<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:offices,name,',
            'location_reference' => 'required|array',
            'location_reference.place_id' => 'required|string|valid_google_place_id', // Custom Rule
            'location_reference.address' => 'required|string',
            'location_reference.latitude' => 'required|numeric|between:-90,90',
            'location_reference.longitude' => 'required|numeric|between:-180,180',
            'capacity' => 'required|integer|min:1|max:1000',
            'amenities' => 'required|array|min:1',
            'amenities.*' => 'required|string|in:HIGH_SPEED_WIFI,PARKING,MEETING_ROOMS,COFFEE_BAR,PRINTER,SECURITY_24_7',
            'contact_email' => 'required|email|unique:offices,contact_email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name is required',
            'name.string' => 'name must be a string',
            'name.max' => 'name must be less than 255 characters',
            'name.unique' => 'this name already exists',
            'location_reference.required' => 'location_reference is required',
            'location_reference.array' => 'location_reference must be an array of values',
            'location_reference.place_id.required' => 'place_id is required',
            'location_reference.place_id.string' => 'place_id must be a string',
            'location_reference.place_id.valid_google_place_id' => 'place_id is not valid',
            'location_reference.address.required' => 'address is required',
            'location_reference.address.string' => 'address must be a string',
            'location_reference.latitude.required' => 'latitude is required',
            'location_reference.latitude.numeric' => 'latitude must be a number',
            'location_reference.latitude.between' => 'latitude must be between -90 and 90',
            'location_reference.longitude.required' => 'longitude is required',
            'location_reference.longitude.numeric' => 'longitude must be a number',
            'location_reference.longitude.between' => 'longitude must be between -180 and 180',
            'capacity.required' => 'capacity is required',
            'capacity.integer' => 'capacity must be an integer',
            'capacity.min' => 'capacity must be at least 1',
            'capacity.max' => 'capacity must be at most 1000',
            'amenities.required' => 'amenities is required',
            'amenities.array' => 'amenities must be an array of values',
            'amenities.*.required' => 'amenity is required',
            'amenities.*.string' => 'amenity must be a string',
            'amenities.*.in' => 'amenity is not valid',
            'contact_email.required' => 'contact_email is required',
            'contact_email.email' => 'contact_email must be a valid email',
            'contact_email.unique' => 'this contact_email already exists',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
