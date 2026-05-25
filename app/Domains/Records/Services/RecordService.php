<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Repositories\RecordRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RecordService
{
    public function __construct(
        protected RecordRepository $recordRepository,
        protected CounterPartyService $counterpartyService,
        protected TypeService $typeService,
        protected AreaService $areaService,
        protected StateService $stateService,
    ) {}

    private function createRecordArray(string $number, string $title, int $stateId, int $typeId, int $counterpartyId, string $createdUpdatedAt): array
    {
        $newRecord = [
            'number' => $number,
            'title' => $title,
            'state_id' => $stateId,
            'type_id' => $typeId,
            'counterparty_id' => $counterpartyId,
            'created_at' => $createdUpdatedAt,
            'updated_at' => $createdUpdatedAt,
        ];

        return $newRecord;
    }

    private function createRecordMovementArray(int $recordId, int $areaId, string $associatedDoc, string $movedAt, int $userId): array
    {
        $newRecordMovement = [
            'record_id' => $recordId,
            'to_area_id' => $areaId,
            'associated_doc' => $associatedDoc,
            'moved_at' => $movedAt,
            'user_id' => $userId,
        ];

        return $newRecordMovement;
    }

    public function addRecord(array $newRecordData): Record
    {
        if (empty($newRecordData['type_id']) && ! empty($newRecordData['type_name'])) {
            $newRecordData['type_id'] = $this->typeService->createGetId($newRecordData['type_name']);
        }

        if (empty($newRecordData['counterparty_id']) && ! empty($newRecordData['counterparty_name'])) {
            $newRecordData['counterparty_id'] = $this->counterpartyService->createGetId($newRecordData['counterparty_name']);
        }

        if (empty($newRecordData['area_id']) && ! empty($newRecordData['area_name'])) {
            $newRecordData['area_id'] = $this->areaService->createGetId($newRecordData['area_name']);
        }

        $newRecord = $this->createRecordArray($newRecordData['number'], $newRecordData['title'], $newRecordData['state_id'], $newRecordData['type_id'], $newRecordData['counterparty_id'], $newRecordData['created_at']);

        $newRecordId = $this->recordRepository->createGetId($newRecord);

        $newRecordMovement = $this->createRecordMovementArray($newRecordId, $newRecordData['area_id'], $newRecordData['associated_doc'], $newRecordData['created_at'], Auth::user()->id);

        $newRecordMovementId = $this->recordRepository->createRecordMovementGetId($newRecordMovement);

        return $this->recordRepository->updateRecordLastMovement($newRecordId, $newRecordMovementId, $newRecordData['created_at']);
    }

    public function addMovement(array $movementData): Record
    {
        if (empty($movementData['area_id']) && ! empty($movementData['area_name'])) {
            $movementData['area_id'] = $this->areaService->createGetId($movementData['area_name']);
        }

        $newRecordMovement = $this->createRecordMovementArray($movementData['id'], $movementData['area_id'], $movementData['associated_doc'], $movementData['date'], Auth::user()->id);
        $newRecordMovementId = $this->recordRepository->createRecordMovementGetId($newRecordMovement);

        return $this->recordRepository->updateRecordLastMovement($movementData['id'], $newRecordMovementId, $movementData['date']);
    }

    public function updateRecord(array $recordData): Record
    {
        return $this->recordRepository->updateRecord($recordData);
    }

    public function index(): Collection
    {
        return $this->recordRepository->getIndex();
    }

    public function getRecordMovements(int $recordId): Collection
    {
        return $this->recordRepository->getRecordMovements($recordId);
    }

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
                'history' => $this->getRecordMovements($record->id)->toArray(),
                'counterparty' => $this->counterpartyService->getById($record->counterparty_id)->name,
                'updated_at' => $record->updated_at,
            ];
        }

        return $recordsArray;
    }

    public function getRecordById(string $id): Record
    {
        return $this->recordRepository->getRecordById($id);
    }

    public function deleteRecord(int $recordId): int
    {
        return $this->recordRepository->deleteRecord($recordId);
    }

    public function generateExcelReport(array $reportData): ExcelReportService
    {
        $excelFilePath = new ExcelReportService($reportData);

        return $excelFilePath;
    }
}
