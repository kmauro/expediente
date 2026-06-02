<?php

namespace App\Domains\Files\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

class ImportFileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                File::types(['xlsx', 'xls'])->max(10 * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El archivo es obligatorio.',
            'file.file' => 'El archivo debe ser un archivo válido.',
            'file.mimes' => 'El archivo debe ser un archivo de Excel (.xlsx o .xls).',
            'file.max' => 'El archivo no debe superar los 10 MB.',
        ];
    }

    /**
     * @return array{file: UploadedFile}
     */
    public function validatedNewData(): array
    {
        /** @var array{file: UploadedFile} $validated */
        $validated = $this->validated();

        return $validated;
    }
}
