<?php

namespace App\Domains\Files\Services;

use App\Domains\Files\Imports\FileImport;
use App\Domains\Records\Repositories\RecordRepository;
use App\Domains\Records\Services\CounterPartyService;
use App\Domains\Records\Services\RecordService;
use App\Domains\Records\Services\TypeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileService
{
    public function __construct(
        protected RecordRepository $recordRepository,
        protected CounterPartyService $counterpartyService,
        protected TypeService $typeService,
        protected RecordService $recordService,
    ) {}

    public function searchReport(array $searchData): array
    {
        $records = $this->recordRepository->searchReport($searchData);
        $recordsArray = [];
        foreach ($records as $record) {
            $recordsArray[] = [
                'id' => $record->id,
                'title' => $record->title,
                'number' => $record->number,
                'type' => $this->typeService->getById($record->type_id)->name,
                'history' => $this->recordService->getRecordMovements($record->id)->toArray(),
                'counterparty' => $this->counterpartyService->getById($record->counterparty_id)->name,
                'last_movement' => $this->recordService->getLastMovement($record->id),
            ];
        }

        return $recordsArray;
    }

    public function generateExcelReport(array $reportData): BinaryFileResponse
    {
        $excelFilePath = new ExcelReportService($reportData);

        return Excel::download($excelFilePath, 'reporte_expedientes.xlsx');
    }

    /**
     * @return array{
     *     total_rows: int,
     *     parsed_rows: array<int, array<int, mixed>>,
     *     normalized_rows: array<int, array<int, mixed>>
     * }
     */
    public function processExcelFile(UploadedFile $file): array
    {
        $filePath = $this->storeUploadedFile($file);

        try {
            $sheets = Excel::toCollection(new FileImport, $filePath, 'local');
            $rows = $sheets->first(default: collect());
            $parsedRows = $this->parseRows($rows);
            $normalizedRows = $this->normalizeRows($parsedRows);
        } finally {
            Storage::disk('local')->delete($filePath);
        }

        return [
            'total_rows' => count($normalizedRows),
            'parsed_rows' => $parsedRows,
            'normalized_rows' => $normalizedRows,
        ];
    }

    /**
     * Sector para parsear la estructura original del Excel.
     *
     * @return array<int, array<int, mixed>>
     */
    private function parseRows(Collection $rows): array
    {
        return $rows
            ->map(fn (Collection|array $row): array => collect($row)->values()->all())
            ->filter(fn (array $row): bool => collect($row)
                ->filter(fn (mixed $value): bool => filled($value))
                ->isNotEmpty())
            ->values()
            ->all();
    }

    /**
     * Sector para normalizar valores antes de persistirlos o validarlos.
     *
     * @param  array<int, array<int, mixed>>  $rows
     * @return array<int, array<int, mixed>>
     */
    private function normalizeRows(array $rows): array
    {
        return collect($rows)
            ->map(fn (array $row): array => collect($row)
                ->map(fn (mixed $value): mixed => is_string($value) ? trim($value) : $value)
                ->map(fn (mixed $value): mixed => $value === '' ? null : $value)
                ->values()
                ->all())
            ->values()
            ->all();
    }

    private function storeUploadedFile(UploadedFile $file): string
    {
        $filePath = $file->store('imports', 'local');

        if (! is_string($filePath) || $filePath === '') {
            throw new \RuntimeException('No se pudo guardar temporalmente el archivo importado.');
        }

        return $filePath;
    }
}
