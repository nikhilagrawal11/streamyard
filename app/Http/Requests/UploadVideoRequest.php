<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadVideoRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        // Only validate video file on create (POST)
        if ($this->isMethod('post')) {
            $rules['video'] = [
                'required',
                'file',
                'mimes:mp4,avi,mov,wmv,flv,webm,mkv',
                'max:1048576', // 1GB in KB
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'Video title is required.',
            'title.max' => 'Video title cannot exceed 255 characters.',
            'video.required' => 'Please select a video file to upload.',
            'video.file' => 'Invalid file format.',
            'video.mimes' => 'Video must be in one of the following formats: MP4, AVI, MOV, WMV, FLV, WebM, MKV.',
            'video.max' => 'Video file size cannot exceed 1GB.',
        ];
    }
}
