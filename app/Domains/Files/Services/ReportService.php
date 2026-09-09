<?php

namespace App\Domains\Files\Services;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Models\RecordMovement;
use App\Domains\Records\Repositories\RecordRepository;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    public function __construct(protected RecordRepository $recordRepository) {}

    /**
     * @return array<int, array{
     *     id: int,
     *     title: string,
     *     number: string,
     *     type: string,
     *     history: array<int, array{id: int, moved_at: string, area_name: string, associated_doc: string}>,
     *     counterparty: string,
     *     last_movement: string
     * }>
     */
    public function search(string $date): array
    {
        return $this->recordRepository
            ->searchReport($date)
            ->map(fn (Record $record): array => $this->mapRecord($record))
            ->all();
    }

    public function download(string $date): ?BinaryFileResponse
    {
        $records = $this->search($date);

        if ($records === []) {
            return null;
        }

        return Excel::download(new ExcelReportService($records), 'reporte_expedientes.xlsx');
    }

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     number: string,
     *     type: string,
     *     history: array<int, array{id: int, moved_at: string, area_name: string, associated_doc: string}>,
     *     counterparty: string,
     *     last_movement: string
     * }
     */
    private function mapRecord(Record $record): array
    {
        return [
            'id' => $record->id,
            'title' => $record->title,
            'number' => $record->number,
            'type' => $record->type->name,
            'history' => $record->movements
                ->map(fn (RecordMovement $movement): array => [
                    'id' => $movement->id,
                    'moved_at' => (string) $movement->moved_at,
                    'area_name' => $movement->area->name,
                    'associated_doc' => $movement->associated_doc,
                ])
                ->all(),
            'counterparty' => $record->counterparty->name,
            'last_movement' => $this->formatMovement($record->lastMovement),
        ];
    }

    private function formatMovement(?RecordMovement $movement): string
    {
        if ($movement === null) {
            return 'Sin movimientos';
        }

        return sprintf(
            '%s Enviado a %s: %s',
            $movement->moved_at,
            $movement->area->name,
            $movement->associated_doc,
        );
    }
}
