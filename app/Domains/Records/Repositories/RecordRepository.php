<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Record;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Domains\Records\Models\RecordMovement;

class RecordRepository
{
    public function getAllForIndex() :Collection
    {
        $records = DB::table('records')->select('id','title','number', 'updated_at')->orderBy('updated_at', 'desc')->get();
        return $records;
    }

    public function getRecordById(String $id) : Record
    {
        $record = Record::findOrFail($id);
        return $record;
    }

    public function addRecord(array $recordData) : Record
    {
        return Record::create($recordData);
    }

    public function createGetId(array $newRecordData) : int
    {
        $newRecord = Record::create($newRecordData);
        return $newRecord->id;
    }

    public function newRecordMovement(array $newRecordMovement) : int
    {
        $newRecordMovement = RecordMovement::create($newRecordMovement);
        return $newRecordMovement->id;
    }

    public function updateRecordLastMovement(int $recordId, int $lastMovementId) : Record
    {
        $record = Record::find($recordId);
        $record->last_movement_id = $lastMovementId;
        $record->save();
        return $record;
    }


}
