<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class EventRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $eventId = $this->route('event') ? $this->route('event')->id : null;
    $request = $this->all();

    if (isset($request['is_set']) && count($request) == 1) {
      return [
        'is_set' => 'boolean',
      ];
    }

    return [
      'name' => 'required|string|max:255|unique:events,name,' . $eventId,
      'start_time' => 'required|date_format:Y-m-d H:i',
      'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
      'location_id' => 'required|exists:locations,id',
      'is_set' => 'boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'The name field is required.',
      'name.string' => 'The name must be a string.',
      'name.max' => 'The name must not be greater than 255 characters.',
      'name.unique' => 'The name has already been taken.',
      'start_time.required' => 'The start time field is required.',
      'start_time.date_format' => 'The start time does not match the format Y-m-d H:i.',
      'end_time.required' => 'The end time field is required.',
      'end_time.date_format' => 'The end time does not match the format Y-m-d H:i.',
      'end_time.after' => 'The end time must be a date after start time.',
      'location_id.required' => 'The location field is required.',
      'location_id.exists' => 'The selected location does not exist.',
      'is_set.boolean' => 'The is_set field must be true or false.',
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
