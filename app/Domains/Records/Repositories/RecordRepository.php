<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Record;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Domains\Records\Models\RecordMovement;

class RecordRepository
{
    public function createGetId(array $newRecordData) : int
    {
        $newRecord = Record::create($newRecordData);
        return $newRecord->id;
    }

    public function createRecordMovementGetId(array $newRecordMovement) : int
    {
        $newRecordMovement = RecordMovement::create($newRecordMovement);
        return $newRecordMovement->id;
    }

    public function updateRecord(array $recordData) : Record
    {
        $record = Record::find($recordData['id']);
        $record->updateOrFail($recordData);
        return $record;
    }

    public function updateRecordLastMovement(int $recordId, int $lastMovementId) : Record
    {
        $record = Record::find($recordId);
        $record->last_movement_id = $lastMovementId;
        $record->save();
        return $record;
    }

    public function getIndex() :Collection
    {
        $records = DB::table('records')
        ->select('records.id','title','number', 'updated_at', 'states.color', 'state_id')
        ->join('states', 'records.state_id', '=', 'states.id')
        ->orderBy('updated_at', 'desc')->get();
        return $records;
    }

    public function getRecordById(String $id) : Record
    {
        $record = Record::findOrFail($id);
        return $record;
    }

    public function deleteRecord(int $recordId) : int
    {
        DB::table('records')->delete($recordId);
        return 1;
    }


}
