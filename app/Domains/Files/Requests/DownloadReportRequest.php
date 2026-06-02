<?php

namespace App\Domains\Files\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DownloadReportRequest extends FormRequest
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
            'records' => ['required', 'array', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'records.required' => 'Debe seleccionar al menos un expediente para descargar el reporte.',
            'records.array' => 'El formato de los expedientes seleccionados es inválido.',
            'records.min' => 'Debe seleccionar al menos un expediente para descargar el reporte.',
        ];
    }

    /**
     * @return array{records}
     */
    public function validatedNewData(): array
    {
        /** @var array{records} $validated */
        $validated = $this->validated();

        return $validated;
    }
}
