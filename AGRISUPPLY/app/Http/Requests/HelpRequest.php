<?php

// app/Http/Requests/HelpRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpRequestValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'required|in:low,medium,high',
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Please provide a subject for your help request.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'description.required' => 'Please describe your problem or concern.',
            'description.min' => 'Description must be at least 10 characters long.',
            'priority.required' => 'Please select a priority level.',
            'priority.in' => 'Priority must be low, medium, or high.',
        ];
    }
}