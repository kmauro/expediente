<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Repositories\RecordRepository;
use App\Domains\Records\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class RecordService
{
    public function __construct(
        protected RecordRepository $recordRepository,
        protected CounterPartyService $counterpartyService,
        protected TypeService $typeService,
        protected AreaService $areaService,
        protected StateService $stateService,
        ) {}


    public function getAllRecords() : Collection
    {
        return $this->recordRepository->getAllForIndex();
    }

    public function getRecordById(String $id) : Record
    {
        return $this->recordRepository->getRecordById($id);
    }


    public function addRecord(array $newRecordData) : Record
    {
        if(!$newRecordData['type_id'] && !empty($newRecordData['type_name'])){
            $newRecordData['type_id'] = $this->typeService->createGetId($newRecordData['type_name']);
        }

        if(!$newRecordData['counterparty_id'] && !empty($newRecordData['counterparty_name'])){
            $newRecordData['counterparty_id'] = $this->counterpartyService->createGetId($newRecordData['counterparty_name']);
        }

        if(!$newRecordData['area_id'] && !empty($newRecordData['area_name'])){
            $newRecordData['area_id'] = $this->areaService->createGetId($newRecordData['area_name']);
        }

        $newRecord = $this->createRecordArray($newRecordData['number'], $newRecordData['title'], $newRecordData['state_id'], $newRecordData['type_id'], $newRecordData['counterparty_id'], $newRecordData['created_at']);

        $newRecordId = $this->recordRepository->createGetId($newRecord);

        $newRecordMovement = $this->createRecordMovementArray($newRecordId, $newRecordData['area_id'], $newRecordData['associated_doc'], $newRecordData['created_at'], Auth::user()->id);

        $newRecordMovementId = $this->recordRepository->newRecordMovement($newRecordMovement);

        return $this->recordRepository->updateRecordLastMovement($newRecordId, $newRecordMovementId);
    }

    private function createRecordArray(string $number, string $title, int $stateId, int $typeId, int $counterpartyId, string $createdUpdatedAt) : array
    {
        $newRecord = [
            'number' => $number,
            'title' => $title,
            'state_id' => $stateId,
            'type_id' => $typeId,
            'counterparty_id' => $counterpartyId,
            'created_at' => $createdUpdatedAt,
            'updated_at' => $createdUpdatedAt
        ];
        return $newRecord;
    }


    private function createRecordMovementArray(int $recordId, int $areaId, string $associatedDoc, string $movedAt, int $userId) : array
    {

        $newRecordMovement = [
            'record_id' => $recordId,
            'to_area_id' => $areaId,
            'associated_doc' => $associatedDoc,
            'moved_at' => $movedAt,
            'user_id' => $userId
        ];
        return $newRecordMovement;
    }
}
