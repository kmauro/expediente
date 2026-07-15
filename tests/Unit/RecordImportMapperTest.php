<?php

use App\Domains\Files\Services\RecordHistoryParser;
use App\Domains\Files\Services\RecordImportMapper;

test('it maps a row with blank current state and optional columns', function () {
    $mapper = new RecordImportMapper(new RecordHistoryParser);

    $record = $mapper->mapRow([
        '2025-05-13',
        'EX-2022-125000131-   -APN-DDYGD#MCT',
        'Rendición de convenio',
        "13/5 Enviado a rendiciones.\n5-6 Enviado a dictámenes.",
        null,
        null,
        null,
    ]);

    expect($record)
        ->not->toBeNull()
        ->and($record['history'])->toHaveCount(2)
        ->and(array_column($record['history'], 'moved_at'))->toBe([
            '2025-05-13',
            '2025-06-05',
        ])
        ->and($record['current_state'])->toBeNull()
        ->and($record['area_name'])->toBe('Enviado a dictámenes.')
        ->and($record['type_name'])->toBeNull()
        ->and($record['counterparty_name'])->toBeNull();
});

test('it maps column E as the current state', function () {
    $mapper = new RecordImportMapper(new RecordHistoryParser);

    $record = $mapper->mapRow([
        45790,
        'EX-2025-50147398-   -APN-DDYGDICYT#JGM',
        'Modificación presupuestaria',
        '13/5 Con proyecto. 20/5 Se firmó la resolución.',
        '26/5 MODIFICACIÓN PRESUPUESTARIA APLICADA',
        'MODIFICACIÓN',
        'PROGRAMA',
    ]);

    expect($record['created_at'])->toBe('2025-05-13')
        ->and($record['current_state'])->toBe([
            'moved_at' => '2025-05-26',
            'associated_doc' => '26/5 MODIFICACIÓN PRESUPUESTARIA APLICADA',
        ]);
});

test('it skips section labels instead of treating them as expedientes', function () {
    $mapper = new RecordImportMapper(new RecordHistoryParser);

    expect($mapper->mapRow([
        null,
        'EXPEDIENTES EN BUZON PVD',
        null,
        null,
        null,
        null,
        null,
    ]))->toBeNull();
});

test('it assigns a generic current state only when columns D and E are both blank', function () {
    $mapper = new RecordImportMapper(new RecordHistoryParser);

    $record = $mapper->mapRow([
        '2025-07-14',
        'EX-2025-00000001-   -APN-DAYFICYT#JGM',
        'Expediente sin estado',
        null,
        null,
        'OBJETO',
        'PROGRAMA',
    ]);

    expect($record['current_state'])->toBe([
        'moved_at' => '2025-07-14',
        'associated_doc' => 'Sin estado informado',
    ]);
});
