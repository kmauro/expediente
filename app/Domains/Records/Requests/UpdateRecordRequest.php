<?php

namespace App\Domains\Records\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecordRequest extends FormRequest
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
            'id' => ['required', 'int', 'exists:records,id'],
            'title' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:255', 'exists:records,number'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'type_id' => ['required', 'integer', 'exists:types,id'],
            'counterparty_id' => ['required', 'integer', 'exists:counterparties,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Se necesita un expediente valido',
            'id.exists' => 'El expediente no existe',
            'title.required' => 'El título es obligatorio.',
            'number.required' => 'El Nro. de expediente es obligatorio.',
            'number.exists' => 'El Nro. de expediente no existe en la base de datos.',
            'state_id.required' => 'Seleccione un estado.',
            'state_id.exists' => 'El estado seleccionado no existe.',
            'type_id.exists' => 'El objeto seleccionado no existe',
            'counterparty_id.exists' => 'El proveedor o programa seleccionado no existe.'
        ];
    }


    /**
     * @return array{id: int, title: string, number: string, state_id: int, type_id: int, counterparty_id: int}
     */
    public function validatedRecordData(): array
    {
        /** @var array{id: int, title: string, number: string, state_id: int, type_id: int, counterparty_id: int}*/
        $validated = $this->validated();

        return $validated;
    }
}
