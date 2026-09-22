<?php

namespace Modules\Message\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conversation_id' => [
                'nullable',
                'integer',
                'exists:conversations,id',
            ],

            'message' => [
                'required',
                'string',
                'min:1',
                'max:5000',
            ],

            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],

            'min_similarity' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
        ];
    }
}