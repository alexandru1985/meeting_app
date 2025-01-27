<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AttendeeGroupRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $attendeeGroupId = $this->route('attendee_group') ? $this->route('attendee_group')->id : null;
    return [
      'name' => 'required|string|max:255|unique:attendee_groups,name,' . $attendeeGroupId,
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'The name field is required.',
      'name.string' => 'The name must be a string.',
      'name.max' => 'The name must not be greater than 255 characters.',
      'name.unique' => 'The name has already been taken.',
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
