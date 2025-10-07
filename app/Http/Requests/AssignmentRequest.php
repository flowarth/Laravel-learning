<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentRequest extends FormRequest
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
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
        ];
    }

    public function messages()
    {
        return [
            'course_id.required' => 'Kursus wajib diisi',
            'course_id.exists' => 'Kursus tidak ditemukan',
            'title.required' => 'Judul wajib diisi',
            'title.string' => 'Judul harus string',
            'title.max' => 'Judul maksimal 255 karakter',
            'description.required' => 'Deskripsi wajib diisi',
            'description.string' => 'Deskripsi harus string',
            'deadline.required' => 'Tanggal deadline wajib diisi',
            'deadline.date' => 'Tanggal deadline harus tanggal',
            'deadline.after' => 'Tanggal deadline harus setelah hari ini',
        ];
    }
}
