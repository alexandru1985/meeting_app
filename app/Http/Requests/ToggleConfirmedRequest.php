<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ToggleConfirmedRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'event_id' => 'required|exists:events,id',
      'confirmed' => 'required|boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'event_id.required' => 'The event ID field is required.',
      'event_id.exists' => 'The selected event ID is invalid.',
      'confirmed.required' => 'The confirmed field is required.',
      'confirmed.boolean' => 'The confirmed field must be true or false.',
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
