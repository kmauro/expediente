<?php

namespace App\Domains\Files\Services;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class RecordImportMapper
{
    public function __construct(private RecordHistoryParser $historyParser) {}

    /**
     * @param  list<array<int, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function map(array $rows): array
    {
        return collect($rows)
            ->map(fn (array $row): ?array => $this->mapRow($row))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array<string, mixed>|null
     */
    public function mapRow(array $row): ?array
    {
        $number = $this->stringValue($row[1] ?? null);

        if ($number === null || ! Str::startsWith(Str::upper($number), 'EX-')) {
            return null;
        }

        $createdAt = $this->dateValue($row[0] ?? null);

        if ($createdAt === null) {
            return null;
        }

        $history = $this->historyParser->parse($this->stringValue($row[3] ?? null), $createdAt);
        $currentState = $this->historyParser->parseCurrentState(
            $this->stringValue($row[4] ?? null),
            $createdAt,
            $history,
        );

        if ($currentState === null && $history === []) {
            $currentState = [
                'moved_at' => $createdAt,
                'associated_doc' => 'Sin estado informado',
            ];
        }

        $lastHistory = $history === [] ? null : $history[array_key_last($history)];
        $lastKnownState = $currentState ?? $lastHistory;

        return [
            'created_at' => $createdAt,
            'number' => $number,
            'title' => $this->stringValue($row[2] ?? null) ?? 'Sin título informado',
            'history' => $history,
            'current_state' => $currentState,
            'area_name' => Str::limit(
                $lastKnownState['associated_doc'] ?? 'Sin estado informado',
                255,
                '',
            ),
            'area_id' => null,
            'type_name' => $this->stringValue($row[5] ?? null),
            'type_id' => null,
            'counterparty_name' => $this->stringValue($row[6] ?? null),
            'counterparty_id' => null,
            'state_id' => 1,
        ];
    }

    private function stringValue(mixed $value): ?string
    {
        if (! is_scalar($value) && ! $value instanceof \Stringable) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (Throwable) {
                return null;
            }
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'j/n/Y', 'j-n-Y'] as $format) {
            $parsedDate = DateTimeImmutable::createFromFormat("!{$format}", $value);
            $errors = DateTimeImmutable::getLastErrors();

            if ($parsedDate !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $parsedDate->format('Y-m-d');
            }
        }

        return null;
    }
}
