<?php

namespace App\Domains\Files\Services;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReportService implements FromArray, WithColumnWidths, WithCustomStartCell, WithHeadings
{
    protected array $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function array(): array
    {
        return array_map(fn (array $record): array => [
            'number' => $record['number'] ?? '',
            'title' => $record['title'] ?? '',
            'type' => $record['type'] ?? '',
            'counterparty' => $record['counterparty'] ?? '',
            'updated_at' => $record['last_movement'] ?? '',
            'history' => $this->formatHistory($record['history'] ?? 'No hay movimientos'),
        ], $this->records);
    }

    public function headings(): array
    {
        return [
            'Numero',
            'Titulo',
            'Objeto',
            'Proveedor/programa',
            'Ultimo movimiento',
            'Movimientos',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 45,
            'C' => 50,
            'D' => 20,
            'E' => 25,
            'F' => 40,
            'G' => 60,
        ];
    }

    private function formatHistory(array $history): string
    {
        $history = collect($history)
            ->map(fn (array $movement): string => sprintf(
                '%s enviado a %s: %s ',
                $movement['moved_at'] ?? '',
                $movement['area_name'] ?? '',
                $movement['associated_doc'] ?? '',
            ))
            ->implode("\n", $history);

        return $history;
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'F' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}
