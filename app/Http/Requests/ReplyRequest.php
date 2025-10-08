<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyRequest extends FormRequest
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
            'discussion_id' => 'required|exists:discussions,id',
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'discussion_id.required' => 'ID diskusi wajib diisi',
            'discussion_id.exists' => 'ID diskusi tidak valid',
            'content.required' => 'Konten balasan wajib diisi',
            'content.string' => 'Konten balasan harus string',
            'content.max' => 'Konten balasan maksimal 1000 karakter',
        ];
    }
}
