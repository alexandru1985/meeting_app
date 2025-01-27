<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class RemoveAttendeeFromTableRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'attendee_id' => 'required|exists:users,id',
      'table_id' => 'required|exists:tables,id',
      'event_id' => 'required|exists:events,id',
    ];
  }

  public function messages(): array
  {
    return [
      'attendee_id.required' => 'The attendee ID field is required.',
      'attendee_id.exists' => 'The selected attendee ID is invalid.',
      'table_id.required' => 'The table ID field is required.',
      'table_id.exists' => 'The selected table ID is invalid.',
      'event_id.required' => 'The event ID field is required.',
      'event_id.exists' => 'The selected event ID is invalid.',
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
