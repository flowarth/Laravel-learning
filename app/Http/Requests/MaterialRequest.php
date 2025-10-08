<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'course_id.required' => 'ID mata kuliah wajib diisi',
            'course_id.exists' => 'ID mata kuliah tidak valid',
            'title.required' => 'Judul materi wajib diisi',
            'title.string' => 'Judul materi harus string',
            'title.max' => 'Judul materi maksimal 255 karakter',
            'file.required' => 'File materi wajib diisi',
            'file.file' => 'File materi harus file',
            'file.mimes' => 'File materi harus PDF, DOC, atau DOCX',
            'file.max' => 'File materi maksimal 10MB',
        ];
    }
}
