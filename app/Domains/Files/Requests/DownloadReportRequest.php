<?php

namespace App\Domains\Files\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DownloadReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', Rule::date()->format('Y-m-d')],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Debe seleccionar una fecha para descargar el reporte.',
            'date.date_format' => 'La fecha debe tener el formato AAAA-MM-DD.',
        ];
    }

    public function validatedDate(): string
    {
        /** @var array{date: string} $validated */
        $validated = $this->validated();

        return $validated['date'];
    }
}
