<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleStreamRequest extends FormRequest
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
            'video_upload_id' => 'required|exists:video_uploads,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'nullable|integer|min:1|max:1440', // Max 24 hours
            'auto_start' => 'boolean',
            'allow_chat' => 'boolean',
            'record_broadcast' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'video_upload_id.required' => 'Please select a video to schedule.',
            'video_upload_id.exists' => 'Selected video does not exist.',
            'title.required' => 'Schedule title is required.',
            'title.max' => 'Schedule title cannot exceed 255 characters.',
            'scheduled_at.required' => 'Scheduled time is required.',
            'scheduled_at.after' => 'Scheduled time must be in the future.',
            'duration.min' => 'Duration must be at least 1 minute.',
            'duration.max' => 'Duration cannot exceed 24 hours (1440 minutes).',
        ];
    }

    protected function prepareForValidation()
    {
        // Ensure the video_upload belongs to the authenticated user
        if ($this->video_upload_id) {
            $videoUpload = auth()->user()->videoUploads()->find($this->video_upload_id);
            if (!$videoUpload) {
                $this->merge(['video_upload_id' => null]);
            }
        }
    }
}
