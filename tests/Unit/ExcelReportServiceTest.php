<?php

use App\Domains\Files\Services\ExcelReportService;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

function reportExportRecord(array $overrides = []): array
{
    return array_merge([
        'number' => 'EX-2026-1',
        'title' => 'Expediente',
        'type' => 'Contratación',
        'counterparty' => 'Proveedor',
        'last_movement' => '2026-06-30 Enviado a Mesa: Documento',
        'history' => [],
    ], $overrides);
}

test('it defines the spreadsheet structure and styles', function () {
    $export = new ExcelReportService([reportExportRecord()]);

    expect($export)
        ->toBeInstanceOf(WithStyles::class)
        ->and($export->headings())->toBe([
            'Numero',
            'Titulo',
            'Objeto',
            'Proveedor/programa',
            'Ultimo movimiento',
            'Movimientos',
        ])
        ->and($export->columnWidths())->toBe([
            'A' => 28,
            'B' => 60,
            'C' => 28,
            'D' => 36,
            'E' => 55,
            'F' => 70,
        ])
        ->and($export->styles(new Worksheet))->toHaveKeys([1, 'A:F', 'B', 'E', 'F']);
});

test('it formats movement history and accepts an empty history', function () {
    $export = new ExcelReportService([
        reportExportRecord(),
        reportExportRecord([
            'history' => [
                [
                    'moved_at' => '2026-06-29',
                    'area_name' => 'Mesa',
                    'associated_doc' => 'Primer documento',
                ],
                [
                    'moved_at' => '2026-06-30',
                    'area_name' => 'Compras',
                    'associated_doc' => 'Segundo documento',
                ],
            ],
        ]),
    ]);
    $rows = $export->array();

    expect($rows[0]['history'])->toBe('')
        ->and($rows[1]['history'])->toBe(
            "2026-06-29 enviado a Mesa: Primer documento \n2026-06-30 enviado a Compras: Segundo documento ",
        );
});

test('it neutralizes spreadsheet formulas', function (string $value) {
    $export = new ExcelReportService([
        reportExportRecord(['title' => $value]),
    ]);

    expect($export->array()[0]['title'])->toBe("'{$value}");
})->with([
    'equals formula' => '=1+1',
    'plus formula' => '+1+1',
    'minus formula' => '-1+1',
    'at formula' => '@SUM(A1:A2)',
    'leading whitespace' => '   =1+1',
]);
