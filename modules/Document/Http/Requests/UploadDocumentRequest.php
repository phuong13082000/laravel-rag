<?php

namespace Modules\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:txt,md,pdf',
            ],
            'title' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
