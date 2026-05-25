<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Models\RecordMovement;
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

    public function getIndex(): Collection
    {
        $records = DB::table('records')
            ->select('records.id', 'title', 'number', 'updated_at', 'states.color', 'state_id')
            ->join('states', 'records.state_id', '=', 'states.id')
            ->orderBy('updated_at', 'desc')->get();

        return $records;
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
