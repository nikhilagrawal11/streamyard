<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStreamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:live,pre_recorded',
            'scheduled_at' => 'nullable|date|after:now',
            'max_participants' => 'nullable|integer|min:2|max:50',
            'allow_chat' => 'boolean',
            'record_stream' => 'boolean',
            'auto_start' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Stream title is required.',
            'title.max' => 'Stream title cannot exceed 255 characters.',
            'type.required' => 'Stream type is required.',
            'type.in' => 'Stream type must be either live or pre-recorded.',
            'scheduled_at.after' => 'Scheduled time must be in the future.',
            'max_participants.min' => 'Maximum participants must be at least 2.',
            'max_participants.max' => 'Maximum participants cannot exceed 50.',
        ];
    }
}
