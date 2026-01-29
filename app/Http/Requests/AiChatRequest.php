<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); 
    }

    public function rules(): array
    {
        return [
            'message' => ['required','string','max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Vui lòng nhập nội dung.',
        ];
    }
}
