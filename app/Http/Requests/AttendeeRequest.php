<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AttendeeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $attendeeId = $this->route('user');
    return [
      'name' => 'required|string|max:255|unique:users,name,' . $attendeeId,
      'company_id' => 'required|exists:companies,id',
      'attendee_group_id' => 'required|exists:attendee_groups,id',
      'confirmed' => 'required|boolean',
      'event_id' => 'required|exists:events,id',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'The name field is required.',
      'name.string' => 'The name must be a string.',
      'name.max' => 'The name must not be greater than 255 characters.',
      'name.unique' => 'The name has already been taken.',
      'company_id.required' => 'The company ID field is required.',
      'company_id.exists' => 'The selected company ID is invalid.',
      'attendee_group_id.required' => 'The attendee group ID field is required.',
      'attendee_group_id.exists' => 'The selected attendee group ID is invalid.',
      'confirmed.required' => 'The confirmed field is required.',
      'confirmed.boolean' => 'The confirmed field must be true or false.',
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
