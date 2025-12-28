<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Only authenticated users can update their password
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => __('lang.current_password_required') ?? 'The current password field is required.',
            'current_password.current_password' => __('lang.current_password_incorrect') ?? 'The current password is incorrect.',
            'password.required' => __('lang.password_required') ?? 'The password field is required.',
            'password.confirmed' => __('lang.password_confirmation') ?? 'The password confirmation does not match.',
        ];
    }
}
