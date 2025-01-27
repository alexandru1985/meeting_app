<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class TableRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'seats' => 'required|integer|min:1',
      'location_id' => 'required|exists:locations,id',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'The name field is required.',
      'name.string' => 'The name must be a string.',
      'name.max' => 'The name must not be greater than 255 characters.',
      'name.unique' => 'The name has already been taken.',
      'seats.required' => 'The seats field is required.',
      'seats.integer' => 'The seats field must be an integer.',
      'seats.min' => 'The seats field must be at least 1.',
      'location_id.required' => 'The location field is required.',
      'location_id.exists' => 'The selected location does not exist.',
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
