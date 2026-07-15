<?php

namespace App\Domains\Files\Services;

use DateTimeImmutable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class RecordHistoryParser
{
    private const DATE_PATTERN = '/(?<![\d-])(?<day>0?[1-9]|[12]\d|3[01])\s*(?<separator>[\/-])\s*(?<month>0?[1-9]|1[0-2])(?:\s*\k<separator>\s*(?<year>20\d{2}))?(?!\d)/u';

    private const DOCUMENT_YEAR_PATTERN = '/(?<![\pL\d])[\pL]{2,15}-(?<year>20\d{2})-\d/iu';

    /**
     * @return list<array{moved_at: string, associated_doc: string}>
     */
    public function parse(?string $history, string $fallbackDate): array
    {
        $history = trim((string) $history);

        if ($history === '') {
            return [];
        }

        $fallback = $this->dateFromFormat($fallbackDate);
        $matchCount = preg_match_all(
            self::DATE_PATTERN,
            $history,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL,
        );

        if ($matchCount === false) {
            throw new RuntimeException('No se pudo analizar el historial del expediente.');
        }

        if ($matchCount === 0) {
            return $this->parseUndatedHistory($history, $fallback);
        }

        $entries = [];
        $firstDateOffset = $matches[0][0][1];
        $prefix = $this->normalizeDescription(substr($history, 0, $firstDateOffset));

        if ($prefix !== '') {
            $entries[] = $this->entry($fallback, $prefix);
        }

        $previousDate = $fallback;

        for ($index = 0; $index < $matchCount; $index++) {
            $matchedDate = $matches[0][$index][0];
            $matchedDateOffset = $matches[0][$index][1];
            $descriptionOffset = $matchedDateOffset + strlen($matchedDate);
            $nextDateOffset = $matches[0][$index + 1][1] ?? strlen($history);
            $description = $this->normalizeDescription(substr(
                $history,
                $descriptionOffset,
                $nextDateOffset - $descriptionOffset,
            ));
            $date = $this->resolveDate(
                day: (int) $matches['day'][$index][0],
                month: (int) $matches['month'][$index][0],
                explicitYear: $matches['year'][$index][0] !== null
                    ? (int) $matches['year'][$index][0]
                    : null,
                documentYear: $this->documentYear($description),
                referenceDate: $previousDate,
            );
            $previousDate = $date;

            if ($description !== '') {
                $entries[] = $this->entry($date, $description);
            }
        }

        return $entries;
    }

    /**
     * @param  list<array{moved_at: string, associated_doc: string}>  $history
     * @return array{moved_at: string, associated_doc: string}|null
     */
    public function parseCurrentState(?string $currentState, string $fallbackDate, array $history): ?array
    {
        $description = $this->normalizeDescription((string) $currentState);

        if ($description === '') {
            return null;
        }

        $historyLastEntry = $history[array_key_last($history)] ?? null;
        $currentFallbackDate = $historyLastEntry['moved_at'] ?? $fallbackDate;
        $currentStateParts = $this->parse($description, $currentFallbackDate);
        $currentLastPart = $currentStateParts[array_key_last($currentStateParts)] ?? null;

        return [
            'moved_at' => $currentLastPart['moved_at'] ?? $currentFallbackDate,
            'associated_doc' => $description,
        ];
    }

    /**
     * @return list<array{moved_at: string, associated_doc: string}>
     */
    private function parseUndatedHistory(string $history, DateTimeImmutable $fallback): array
    {
        $parts = preg_split('/\R+/u', $history) ?: [$history];

        return collect($parts)
            ->map(fn (string $part): string => $this->normalizeDescription($part))
            ->filter()
            ->map(fn (string $description): array => $this->entry($fallback, $description))
            ->values()
            ->all();
    }

    private function dateFromFormat(string $date): DateTimeImmutable
    {
        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException("La fecha de respaldo [{$date}] no es válida.");
        }

        return $parsedDate;
    }

    private function resolveDate(
        int $day,
        int $month,
        ?int $explicitYear,
        ?int $documentYear,
        DateTimeImmutable $referenceDate,
    ): DateTimeImmutable {
        $year = $explicitYear ?? $documentYear ?? (int) $referenceDate->format('Y');

        if ($explicitYear === null && $documentYear === null) {
            $referenceMonth = (int) $referenceDate->format('n');

            if ($referenceMonth - $month >= 6) {
                $year++;
            }
        }

        if (! checkdate($month, $day, $year)) {
            return $referenceDate;
        }

        return $referenceDate->setDate($year, $month, $day);
    }

    private function documentYear(string $description): ?int
    {
        $matched = preg_match(self::DOCUMENT_YEAR_PATTERN, $description, $matches);

        return $matched === 1 ? (int) $matches['year'] : null;
    }

    private function normalizeDescription(string $description): string
    {
        return Str::squish($description);
    }

    /**
     * @return array{moved_at: string, associated_doc: string}
     */
    private function entry(DateTimeImmutable $date, string $description): array
    {
        return [
            'moved_at' => $date->format('Y-m-d'),
            'associated_doc' => $description,
        ];
    }
}
