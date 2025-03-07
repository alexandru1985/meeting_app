<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'max:255'],
      'email' => ['required', 'email', 'unique:users'],
      'password' => ['required', 'min:8', 'confirmed'],
    ];
  }
}
