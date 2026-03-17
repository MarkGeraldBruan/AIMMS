<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user'); // Get user ID from route parameter
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => 'required|in:user,admin',
            'can_create' => 'nullable|boolean',
            'can_read' => 'nullable|boolean',
            'can_update' => 'nullable|boolean',
            'can_delete' => 'nullable|boolean',
            'can_stock_in' => 'nullable|boolean',
            'can_stock_out' => 'nullable|boolean',
        ];

        // Status is only required for updates
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['status'] = 'required|in:active,inactive';
        } else {
            $rules['status'] = 'nullable|in:active,inactive';
        }

        // Password is required for new users, optional for updates
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the user\'s full name.',
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Please sselect a user role.',
            'role.in' => 'Invalid role selected.',
            'status.required' => 'Please select a user status.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}