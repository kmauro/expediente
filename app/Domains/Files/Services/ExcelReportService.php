<?php

namespace App\Domains\Files\Services;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReportService implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected array $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function array(): array
    {
        return array_map(fn (array $record): array => [
            'number' => $this->sanitizeSpreadsheetValue($record['number'] ?? ''),
            'title' => $this->sanitizeSpreadsheetValue($record['title'] ?? ''),
            'type' => $this->sanitizeSpreadsheetValue($record['type'] ?? ''),
            'counterparty' => $this->sanitizeSpreadsheetValue($record['counterparty'] ?? ''),
            'updated_at' => $this->sanitizeSpreadsheetValue($record['last_movement'] ?? ''),
            'history' => $this->sanitizeSpreadsheetValue($this->formatHistory($record['history'] ?? [])),
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
            'A' => 28,
            'B' => 60,
            'C' => 28,
            'D' => 36,
            'E' => 55,
            'F' => 70,
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
            ->implode("\n");

        return $history;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E8F0'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A:F' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
            'B' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
            'E' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
            'F' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }

    private function sanitizeSpreadsheetValue(mixed $value): string
    {
        $value = (string) $value;

        if (Str::startsWith(Str::ltrim($value), ['=', '+', '-', '@'])) {
            return "'{$value}";
        }

        return $value;
    }
}
