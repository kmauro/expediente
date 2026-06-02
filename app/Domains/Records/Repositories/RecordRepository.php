<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Models\RecordMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecordRepository
{
    public function createGetId(array $newRecordData): int
    {
        $newRecord = Record::create($newRecordData);

        return $newRecord->id;
    }

    public function createRecordMovementGetId(array $newRecordMovement): int
    {
        $newRecordMovement = RecordMovement::create($newRecordMovement);

        return $newRecordMovement->id;
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

    public function getLastMovement(int $recordId)
    {
        $record = Record::find($recordId);
        if ($record->last_movement_id) {
            $recordMovement = RecordMovement::find($record->last_movement_id)->with('area')->first();
            return $recordMovement->moved_at . ' Enviado a ' . $recordMovement->area->name . ': ' . $recordMovement->associated_doc;
        }
    }

    public function searchReport(array $searchData): Collection
    {
        $query = DB::table('records')
            ->select('records.id', 'title', 'number', 'updated_at', 'type_id', 'counterparty_id')
            ->join('states', 'records.state_id', '=', 'states.id');
        if (! empty($searchData['date'])) {
            $query->where('updated_at', '=', $searchData['date']);
        }

        return $query->orderBy('updated_at', 'desc')->get();
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
