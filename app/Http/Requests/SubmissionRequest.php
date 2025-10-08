<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
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
        return [
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file'
        ];
    }

    public function messages()
    {
        return [
            'assignment_id.required' => 'ID tugas wajib diisi',
            'assignment_id.exists' => 'ID tugas tidak valid',
            'file.required' => 'File wajib diisi',
            'file.file' => 'File harus file',
        ];
    }
}
