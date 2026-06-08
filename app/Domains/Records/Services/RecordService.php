<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Repositories\RecordRepository;
use Illuminate\Http\Request;
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

    public function addFileRecord(array $recordData): Record
    {
        $recordData['type_id'] = $this->typeService->findOrCreateGetId($recordData['type_name']);
        $recordData['counterparty_id'] = $this->counterpartyService->findOrCreateGetId($recordData['counterparty_name']);
        $recordData['area_id'] = $this->areaService->findOrCreateGetId($recordData['area_name']);

        $newRecord = $this->createRecordArray($recordData['number'], $recordData['title'], $recordData['state_id'], $recordData['type_id'], $recordData['counterparty_id'], $recordData['created_at']);
        $newRecordId = $this->recordRepository->createGetId($newRecord);

        $historyRecordMovement = $this->createRecordMovementArray($newRecordId, $recordData['area_id'], $recordData['history'], $recordData['created_at'], Auth::user()->id);
        $this->recordRepository->createRecordMovementGetId($historyRecordMovement);

        $newRecordMovement = $this->createRecordMovementArray($newRecordId, $recordData['area_id'], $recordData['associated_doc'], $recordData['created_at'], Auth::user()->id);
        $newRecordMovementId = $this->recordRepository->createRecordMovementGetId($newRecordMovement);

        return $this->recordRepository->updateRecordLastMovement($newRecordId, $newRecordMovementId, $recordData['created_at']);

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

    public function getFields(): array
    {
        $fields = [
            ['name' => 'number',
                'label' => 'Numero'],
            ['name' => 'title',
                'label' => 'Titulo'],
            ['name' => 'type_id',
                'label' => 'Objeto'],
            ['name' => 'counterparty_id',
                'label' => 'Proveedor/Programa'],
            ['name' => 'area_id',
                'label' => 'Area'],
        ];

        return $fields;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;
        $field = $request->field;
        $query = $this->recordRepository->getIndexQuery();
        if ($search && $field) {
            switch ($field) {
                case 'title':
                    $this->recordRepository->addWhere($query, 'records.title', 'like', $search);
                    break;
                case 'number':
                    $this->recordRepository->addWhere($query, 'records.number', 'like', $search);
                    break;
                case 'type_id':
                    $this->recordRepository->addWhereHas($query, 'type', 'name', 'like', $search);
                    break;
                case 'area_id':
                    $this->recordRepository->addWhere($query, 'areas.name', 'like', $search);
                    break;
                case 'counterparty_id':
                    $this->recordRepository->addWhereHas($query, 'counterparty', 'name', 'like', $search);
                    break;

            }
        }

        // filtro por tag
        if ($state) {
            $query->where('state_id', $state['id']);
        }

        return $query
            ->get();
    }

    public function getRecordMovements(int $recordId): Collection
    {
        return $this->recordRepository->getRecordMovements($recordId);
    }

    public function getLastMovement(int $recordId)
    {
        return $this->recordRepository->getLastMovement($recordId);
    }

    public function getRecordById(string $id): Record
    {
        return $this->recordRepository->getRecordById($id);
    }

    public function deleteRecord(int $recordId): int
    {
        return $this->recordRepository->deleteRecord($recordId);
    }
}
