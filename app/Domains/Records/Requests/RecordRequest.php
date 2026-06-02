<?php

namespace App\Domains\Records\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Domains\Records\Models\Record;

class RecordRequest extends FormRequest
{
    public function authorize() : bool
    {
        return true;
    }

    /**
     * @return array<string, array<int,mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:2500'],
            'number' => ['required', 'string', 'max:255', Rule::unique(Record::class, 'number')],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'type_id' => ['nullable', 'integer', 'exists:types,id'],
            'type_name'=> ['nullable', 'string', 'max:255'],
            'counterparty_id' => ['nullable', 'integer', 'exists:counterparties,id'],
            'counterparty_name'=> ['nullable', 'string', 'max:255'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'area_name'=> ['nullable', 'string', 'max:255'],
            'created_at' => ['required', 'date'],
            'associated_doc' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'number.required' => 'El Nro. de expediente es obligatorio.',
            'number.unique' => 'El Nro. de expediente ya existe.',
            'state_id.required' => 'Seleccione un estado.',
            'state_id.exists' => 'El estado seleccionado no existe.',
            'type_id.exists' => 'El objeto seleccionado no existe',
            'counterparty_id.exists' => 'El proveedor o programa seleccionado no existe.',
            'area_id' => 'El area seleccionada no existe.',
            'created_at.required' => 'Seleccione una fecha.',
            'associated_doc.required' => 'Ingrese una descripción.'
        ];
    }


    /**
     * @return array{title: string, number: string, state_id: int, type_id: int|null, type_name: string|null, counterparty_id: int|null, counterparty_name: string|null, area_id: int|null, area_name: string|null, created_at: string, associated_doc: string}
     */
    public function validatedRecordData(): array
    {
        /** @var array{title: string, number: string, state_id: int, type_id: int|null, type_name: string|null, counterparty_id: int|null, counterparty_name: string|null, area_id: int|null, area_name: string|null, created_at: string, associated_doc: string}*/
        $validated = $this->validated();

        return $validated;
    }
}
