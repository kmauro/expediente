<?php

namespace App\Domains\Records\Requests;

use App\Domains\Records\Models\RecordMovement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordMovementRequest extends FormRequest
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
            'id' => ['required', 'int', 'exists:records,id'],
            'date' => ['required', 'string', 'max:255'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id', 'required_without:area_name', 'prohibited_if:area_name, not null'],
            'area_name' => ['nullable', 'string', 'max:255'],
            'associated_doc' => ['required', 'string', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Error al seleccionar el expediente',
            'id.exists' => 'El expediente seleccionado no existe',
            'area_id' => 'El area seleccionada no existe.',
            'date.required' => 'Seleccione una fecha.',
            'associated_doc.required' => 'Ingrese una descripción.',
            'area_id.required_without' => 'Debe seleccionar un área o ingresar una nueva.',
            'area_id.prohibited_with' => 'No puede seleccionar un área existente y crear una nueva al mismo tiempo.',
        ];
    }

    /**
     * @return array{date: string, area_id: int|null, area_name: string|null, associated_doc: string}
     */
    public function validatedNewData(): array
    {
        /** @var array{date: string, area_id: int|null, area_name: string|null, associated_doc: string} $validated */
        $validated = $this->validated();

        return $validated;
    }
}
