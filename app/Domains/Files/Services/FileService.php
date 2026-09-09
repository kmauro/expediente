<?php

namespace App\Domains\Files\Services;

use App\Domains\Files\Imports\FileImport;
use App\Domains\Records\Services\RecordService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FileService
{
    public function __construct(
        protected RecordService $recordService,
        protected RecordImportMapper $recordImportMapper,
    ) {}

    public function processExcelFile(UploadedFile $file): array
    {
        $filePath = $this->storeUploadedFile($file);

        try {
            $sheets = Excel::toCollection(new FileImport, $filePath, 'local');
            $parsedRows = $sheets
                ->flatMap(fn (Collection|array $sheet): array => $this->parseRows(collect($sheet)->skip(1)))
                ->values()
                ->all();
            $normalizedRows = $this->normalizeRows($parsedRows);
            $newRecords = $this->recordImportMapper->map($normalizedRows);
            $recordIds = DB::transaction(function () use ($newRecords): array {
                return collect($newRecords)
                    ->map(fn (array $recordData): int => $this->recordService->addFileRecord($recordData)->id)
                    ->unique()
                    ->values()
                    ->all();
            }, attempts: 3);
        } finally {
            Storage::disk('local')->delete($filePath);
        }

        return [
            'total_rows' => count($recordIds),
            'source_rows' => count($normalizedRows),
            'skipped_rows' => count($normalizedRows) - count($newRecords),
        ];
    }

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

    private function normalizeRows(array $rows): array
    {
        return collect($rows)
            ->map(fn (array $row): array => collect($row)
                ->map(fn (mixed $value): mixed => is_string($value) ? trim($value) : $value)
                ->map(fn (mixed $value): mixed => $value === '' ? null : $value)
                ->take(8)
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
