<?php

namespace App\Domains\Records\Requests;

use App\Domains\Records\Models\State;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique(State::class, 'name')],
            'color' => ['required', 'string', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Este registro ya existe',
            'color.required' => 'Seleccione un color.'
        ];
    }

    /**
     * @return array{name: string, color: string}
     */
    public function validatedNewData(): array
    {
        /** @var array{name: string, color: string} $validated */
        $validated = $this->validated();

        return $validated;
    }
}
