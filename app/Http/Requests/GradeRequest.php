<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
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
            'score' => 'required|integer|min:0|max:100'
        ];
    }

    public function messages()
    {
        return [
            'score.required' => 'Nilai wajib diisi',
            'score.integer' => 'Nilai harus angka',
            'score.min' => 'Nilai minimal 0',
            'score.max' => 'Nilai maksimal 100',
        ];
    }
}
