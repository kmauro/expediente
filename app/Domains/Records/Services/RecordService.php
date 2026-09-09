<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Record;
use App\Domains\Records\Repositories\RecordRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RecordService
{
    private const DEFAULT_COUNTERPARTY_NAME = 'Sin contraparte informada';

    private const DEFAULT_TYPE_NAME = 'Sin objeto informado';

    private ?int $defaultCounterpartyId = null;

    private ?int $defaultTypeId = null;

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
        $userId = Auth::id();

        if (! is_int($userId)) {
            throw new \LogicException('La importación de expedientes requiere un usuario autenticado.');
        }

        $typeName = filled($recordData['type_name'] ?? null)
            ? $recordData['type_name']
            : self::DEFAULT_TYPE_NAME;
        $counterpartyName = filled($recordData['counterparty_name'] ?? null)
            ? $recordData['counterparty_name']
            : self::DEFAULT_COUNTERPARTY_NAME;
        $typeId = $this->typeService->findOrCreateGetId($typeName);
        $counterpartyId = $this->counterpartyService->findOrCreateGetId($counterpartyName);
        $areaId = $this->areaService->findOrCreateGetId($recordData['area_name']);
        $newRecordData = $this->createRecordArray(
            $recordData['number'],
            $recordData['title'],
            $recordData['state_id'],
            $typeId,
            $counterpartyId,
            $recordData['created_at'],
        );
        $record = $this->recordRepository->firstOrCreateRecord($newRecordData);

        $this->updateMissingImportedMetadata(
            $record,
            $recordData,
            $typeId,
            $counterpartyId,
        );

        $lastMovement = null;

        foreach ($recordData['history'] as $historyEntry) {
            $historyMovement = $this->createRecordMovementArray(
                $record->id,
                $areaId,
                $historyEntry['associated_doc'],
                $historyEntry['moved_at'],
                $userId,
            );
            $lastMovement = [
                'id' => $this->recordRepository->firstOrCreateRecordMovementGetId($historyMovement),
                'moved_at' => $historyEntry['moved_at'],
            ];
        }

        if ($recordData['current_state'] !== null) {
            $currentMovement = $this->createRecordMovementArray(
                $record->id,
                $areaId,
                $recordData['current_state']['associated_doc'],
                $recordData['current_state']['moved_at'],
                $userId,
            );
            $lastMovement = [
                'id' => $this->recordRepository->updateOrCreateCurrentRecordMovementGetId($record, $currentMovement),
                'moved_at' => $recordData['current_state']['moved_at'],
            ];
        }

        if ($lastMovement !== null) {
            return $this->recordRepository->updateRecordLastMovementIfNewer(
                $record->id,
                $lastMovement['id'],
                $lastMovement['moved_at'],
            );
        }

        return $record;

    }

    private function updateMissingImportedMetadata(
        Record $record,
        array $recordData,
        int $typeId,
        int $counterpartyId,
    ): void {
        if ($record->wasRecentlyCreated) {
            return;
        }

        $updates = [];

        if (filled($recordData['type_name'] ?? null) && $record->type_id === $this->defaultTypeId()) {
            $updates['type_id'] = $typeId;
        }

        if (filled($recordData['counterparty_name'] ?? null) && $record->counterparty_id === $this->defaultCounterpartyId()) {
            $updates['counterparty_id'] = $counterpartyId;
        }

        if ($record->title === 'Sin título informado' && filled($recordData['title'] ?? null)) {
            $updates['title'] = $recordData['title'];
        }

        if ($updates !== []) {
            $record->update($updates);
        }
    }

    private function defaultTypeId(): int
    {
        return $this->defaultTypeId ??= $this->typeService->findOrCreateGetId(self::DEFAULT_TYPE_NAME);
    }

    private function defaultCounterpartyId(): int
    {
        return $this->defaultCounterpartyId ??= $this->counterpartyService->findOrCreateGetId(self::DEFAULT_COUNTERPARTY_NAME);
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

    public function getLastMovement(int $recordId): ?string
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
