<?php

namespace App\Domains\Files\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
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
            'date' => ['nullable', Rule::date()->format('Y-m-d')],
        ];
    }

    public function messages(): array
    {
        return [
            'date.date_format' => 'La fecha debe tener el formato AAAA-MM-DD.',
        ];
    }

    public function validatedDate(): ?string
    {
        $date = $this->validated('date');

        return is_string($date) ? $date : null;
    }
}
