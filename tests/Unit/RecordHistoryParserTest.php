<?php

use App\Domains\Files\Services\RecordHistoryParser;

test('it splits inline historical states using dates instead of line breaks', function () {
    $entries = (new RecordHistoryParser)->parse(
        '09/01/2025 Remití el expediente. 24/1 Volvió con observaciones. 28/1 Pasó a la subsecretaría.',
        '2024-01-09',
    );

    expect($entries)
        ->toHaveCount(3)
        ->and(array_column($entries, 'moved_at'))->toBe([
            '2025-01-09',
            '2025-01-24',
            '2025-01-28',
        ])
        ->and(array_column($entries, 'associated_doc'))->toBe([
            'Remití el expediente.',
            'Volvió con observaciones.',
            'Pasó a la subsecretaría.',
        ]);
});

test('it recognizes document years and year rollovers without expediente-specific rules', function () {
    $entries = (new RecordHistoryParser)->parse(
        '26/12 Proyecto enviado PV-2025-144136498-APN-DAYFICYT#JGM 12/1 Devuelto PV-2026-04087522-APN-DAYFICYT#JGM',
        '2025-12-26',
    );

    expect(array_column($entries, 'moved_at'))->toBe([
        '2025-12-26',
        '2026-01-12',
    ]);
});

test('it does not interpret GDE document numbers as dates', function () {
    $entries = (new RecordHistoryParser)->parse(
        'PV-2025-05042129-APN-DAYFICYT#JGM enviado a Jurídicos. 23/01/2025 Se notificó la resolución.',
        '2025-01-15',
    );

    expect($entries)
        ->toHaveCount(2)
        ->and($entries[0]['moved_at'])->toBe('2025-01-15')
        ->and($entries[1]['moved_at'])->toBe('2025-01-23');
});

test('it preserves undated multiline history as separate states', function () {
    $entries = (new RecordHistoryParser)->parse("Primer estado\nSegundo estado", '2025-05-13');

    expect($entries)->toBe([
        ['moved_at' => '2025-05-13', 'associated_doc' => 'Primer estado'],
        ['moved_at' => '2025-05-13', 'associated_doc' => 'Segundo estado'],
    ]);
});

test('it keeps the current state intact and uses its latest date', function () {
    $parser = new RecordHistoryParser;
    $history = $parser->parse('26/12 Estado anterior PV-2025-123-APN-DAYFICYT#JGM', '2025-12-26');
    $currentState = $parser->parseCurrentState(
        "31/12 A despacho\n10-1 A tesorería",
        '2025-12-26',
        $history,
    );

    expect($currentState)->toBe([
        'moved_at' => '2026-01-10',
        'associated_doc' => '31/12 A despacho 10-1 A tesorería',
    ]);
});
