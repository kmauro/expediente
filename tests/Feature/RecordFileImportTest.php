<?php

use App\Domains\Records\Models\Record;
use App\Domains\Records\Models\RecordMovement;
use App\Domains\Records\Services\RecordService;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $roleId = DB::table('roles')->insertGetId(['role_name' => 'admin']);

    DB::table('states')->insert([
        'id' => 1,
        'name' => 'En buzon',
        'color' => 'teal',
    ]);

    $user = User::query()->create([
        'name' => 'Importador',
        'surname' => 'Pruebas',
        'email' => 'importador@example.test',
        'username' => 'importador',
        'phone' => null,
        'role_id' => $roleId,
        'password' => 'password',
    ]);

    $this->actingAs($user);
});

test('it imports every historical state and can be reimported without duplicates', function () {
    $recordData = [
        'created_at' => '2025-05-13',
        'number' => 'EX-2025-50147398-   -APN-DDYGDICYT#JGM',
        'title' => 'Modificación presupuestaria',
        'state_id' => 1,
        'type_name' => null,
        'counterparty_name' => null,
        'area_name' => 'MODIFICACIÓN PRESUPUESTARIA APLICADA',
        'history' => [
            [
                'moved_at' => '2025-05-13',
                'associated_doc' => 'Con proyecto para revisión.',
            ],
            [
                'moved_at' => '2025-05-20',
                'associated_doc' => str_repeat('Detalle extenso del estado histórico. ', 10),
            ],
        ],
        'current_state' => [
            'moved_at' => '2025-05-26',
            'associated_doc' => 'MODIFICACIÓN PRESUPUESTARIA APLICADA',
        ],
    ];
    $service = $this->app->make(RecordService::class);

    $record = $service->addFileRecord($recordData);
    $legacyCurrentMovement = RecordMovement::query()->findOrFail($record->last_movement_id);
    $legacyCurrentMovement->update(['moved_at' => '2025-05-13']);
    $record->update(['updated_at' => '2025-05-13']);
    $reimportedRecord = $service->addFileRecord($recordData);
    $lastMovement = RecordMovement::query()->findOrFail($reimportedRecord->last_movement_id);

    expect($record->id)->toBe($reimportedRecord->id)
        ->and(Record::query()->count())->toBe(1)
        ->and(RecordMovement::query()->count())->toBe(3)
        ->and($lastMovement->associated_doc)->toBe('MODIFICACIÓN PRESUPUESTARIA APLICADA')
        ->and($lastMovement->moved_at)->toBe('2025-05-26')
        ->and($reimportedRecord->updated_at)->toBe('2025-05-26')
        ->and($record->type->name)->toBe('Sin objeto informado')
        ->and($record->counterparty->name)->toBe('Sin contraparte informada');
});

test('it uses the last historical state when column E is blank', function () {
    $record = $this->app->make(RecordService::class)->addFileRecord([
        'created_at' => '2025-05-13',
        'number' => 'EX-2022-125000131-   -APN-DDYGD#MCT',
        'title' => 'Rendición de convenio',
        'state_id' => 1,
        'type_name' => 'RENDICIÓN',
        'counterparty_name' => null,
        'area_name' => 'Enviado a dictámenes.',
        'history' => [
            [
                'moved_at' => '2025-05-13',
                'associated_doc' => 'Enviado a rendiciones.',
            ],
            [
                'moved_at' => '2025-06-05',
                'associated_doc' => 'Enviado a dictámenes.',
            ],
        ],
        'current_state' => null,
    ]);
    $lastMovement = RecordMovement::query()->findOrFail($record->last_movement_id);

    expect(RecordMovement::query()->whereBelongsTo($record)->count())->toBe(2)
        ->and($lastMovement->associated_doc)->toBe('Enviado a dictámenes.')
        ->and($record->updated_at)->toBe('2025-06-05');
});
