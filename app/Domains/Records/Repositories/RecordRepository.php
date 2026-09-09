<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Models\RecordMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecordRepository
{
    public function createGetId(array $newRecordData): int
    {
        $newRecord = Record::create($newRecordData);

        return $newRecord->id;
    }

    public function firstOrCreateRecord(array $recordData): Record
    {
        return Record::firstOrCreate(
            ['number' => $recordData['number']],
            Arr::except($recordData, ['number']),
        );
    }

    public function createRecordMovementGetId(array $newRecordMovement): int
    {
        $newRecordMovement = RecordMovement::create($newRecordMovement);

        return $newRecordMovement->id;
    }

    public function firstOrCreateRecordMovementGetId(array $recordMovementData): int
    {
        $recordMovement = RecordMovement::firstOrCreate(
            Arr::except($recordMovementData, ['user_id']),
            ['user_id' => $recordMovementData['user_id']],
        );

        return $recordMovement->id;
    }

    public function updateOrCreateCurrentRecordMovementGetId(Record $record, array $recordMovementData): int
    {
        $currentMovement = $record->last_movement_id !== null
            ? RecordMovement::find($record->last_movement_id)
            : null;

        if (
            $currentMovement !== null
            && $currentMovement->to_area_id === $recordMovementData['to_area_id']
            && Str::squish($currentMovement->associated_doc) === Str::squish($recordMovementData['associated_doc'])
        ) {
            $currentMovement->update([
                'associated_doc' => $recordMovementData['associated_doc'],
                'moved_at' => $recordMovementData['moved_at'],
                'user_id' => $recordMovementData['user_id'],
            ]);

            return $currentMovement->id;
        }

        return $this->firstOrCreateRecordMovementGetId($recordMovementData);
    }

    public function updateRecord(array $recordData): Record
    {
        $record = Record::find($recordData['id']);
        $record->updateOrFail($recordData);

        return $record;
    }

    public function updateRecordLastMovement(int $recordId, int $lastMovementId, string $movedAt): Record
    {
        $record = Record::find($recordId);
        $record->last_movement_id = $lastMovementId;
        $record->updated_at = $movedAt;
        $record->save();

        return $record;
    }

    public function updateRecordLastMovementIfNewer(int $recordId, int $lastMovementId, string $movedAt): Record
    {
        $record = Record::findOrFail($recordId);

        if ($record->last_movement_id === null || $movedAt >= (string) $record->updated_at) {
            $record->last_movement_id = $lastMovementId;
            $record->updated_at = $movedAt;
            $record->save();
        }

        return $record;
    }

    public function getIndexQuery(): Builder
    {
        return Record::query()
            ->select('records.id', 'title', 'number', 'updated_at', 'states.color', 'state_id', 'areas.name')
            ->join('states', 'records.state_id', '=', 'states.id')
            ->join('record_movements', 'records.last_movement_id', '=', 'record_movements.id')
            ->join('areas', 'areas.id', '=', 'record_movements.to_area_id')
            ->orderBy('updated_at', 'desc');
    }

    public function addWhere(Builder $query, string $field, string $operator, string $search): Builder
    {
        return $query->where($field, $operator, "%{$search}%");
    }

    public function addWhereHas(Builder $query, string $table, string $field, string $operator, string $search)
    {
        return $query->whereHas($table, function ($q) use ($search, $field, $operator) {
            $this->addWhere($q, $field, $operator, $search);
        });
    }

    public function getRecordMovements(int $recordId): Collection
    {
        $movements = DB::table('record_movements')
            ->select('record_movements.id', 'to_area_id', 'associated_doc', 'moved_at', 'areas.name as area_name')
            ->join('areas', 'record_movements.to_area_id', '=', 'areas.id')
            ->where('record_movements.record_id', '=', $recordId)
            ->orderBy('moved_at', 'asc')->get();

        return $movements;
    }

    public function getLastMovement(int $recordId): ?string
    {
        $record = Record::query()
            ->with('lastMovement.area')
            ->findOrFail($recordId);
        $recordMovement = $record->lastMovement;

        if ($recordMovement !== null) {
            return $recordMovement->moved_at.' Enviado a '.$recordMovement->area->name.': '.$recordMovement->associated_doc;
        }

        return null;
    }

    public function searchReport(string $date): Collection
    {
        return Record::query()
            ->select('id', 'title', 'number', 'updated_at', 'type_id', 'counterparty_id', 'last_movement_id')
            ->with([
                'type:id,name',
                'counterparty:id,name',
                'lastMovement:id,to_area_id,moved_at,associated_doc',
                'lastMovement.area:id,name',
                'movements' => fn (HasMany $query): HasMany => $query
                    ->select('id', 'record_id', 'to_area_id', 'associated_doc', 'moved_at')
                    ->with('area:id,name')
                    ->oldest('moved_at')
                    ->oldest('id'),
            ])
            ->where('updated_at', $date)
            ->latest('id')
            ->get();
    }

    public function getRecordById(string $id): Record
    {
        $record = Record::findOrFail($id);

        return $record;
    }

    public function deleteRecord(int $recordId): int
    {
        DB::table('records')->delete($recordId);

        return 1;
    }
}
