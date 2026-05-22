<?php

namespace App\Domains\Records\Requests;

use App\Domains\Records\Models\Area;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique(Area::class, 'name')]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Este registro ya existe'
        ];
    }

    /**
     * @return array{name: string}
     */
    public function validatedAreaData(): array
    {
        /** @var array{name: string} $validated */
        $validated = $this->validated();

        return $validated;
    }
}
