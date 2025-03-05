<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class LocationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $locationId = $this->route('location') ? $this->route('location')->id : null;
    return [
      'name' => 'required|string|max:255|unique:locations,name,' . $locationId,
      'address' => 'required|string|max:255',
      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'The name field is required.',
      'name.string' => 'The name must be a string.',
      'name.max' => 'The name must not be greater than 255 characters.',
      'name.unique' => 'The name has already been taken.',
      'address.required' => 'The address field is required.',
      'address.string' => 'The address must be a string.',
      'address.max' => 'The address must not be greater than 255 characters.',
      'latitude.required' => 'The latitude field is required.',
      'latitude.numeric' => 'The latitude must be a number.',
      'longitude.required' => 'The longitude field is required.',
      'longitude.numeric' => 'The longitude must be a number.',
    ];
  }

  protected function failedValidation(Validator $validator): void
  {
    throw new HttpResponseException(
      response()->json(
        [
          'errors' => $validator->errors(),
        ],
        Response::HTTP_UNPROCESSABLE_ENTITY
      )
    );
  }
}

