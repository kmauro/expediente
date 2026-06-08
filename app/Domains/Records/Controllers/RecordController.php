<?php

namespace App\Domains\Records\Controllers;

use App\Domains\Records\Requests\RecordMovementRequest;
use App\Domains\Records\Requests\RecordRequest;
use App\Domains\Records\Requests\UpdateRecordRequest;
use App\Domains\Records\Services\AreaService;
use App\Domains\Records\Services\CounterPartyService;
use App\Domains\Records\Services\RecordService;
use App\Domains\Records\Services\StateService;
use App\Domains\Records\Services\TypeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RecordController extends Controller
{
    public function __construct(
        protected AreaService $areaService,
        protected StateService $stateService,
        protected CounterPartyService $counterpartyService,
        protected TypeService $typeService,
        protected RecordService $recordService, ) {}

    public function index(Request $request): InertiaResponse
    {
        $records = $this->recordService->index($request);
        $states = $this->stateService->getAll();
        $fields = $this->recordService->getFields();

        return Inertia::render('Records/index', [
            'title' => 'Lista de Expedientes',
            'records' => $records,
            'count' => $records->count(),
            'states' => $states,
            'fields' => $fields,
            'filters' => [
                'field' => $request->field ?? 'number',
                'search' => $request->search ?? '',
                'state' => $request->state ?? '',
            ],
        ]);
    }

    public function showRecord(string $id)
    {
        $record = $this->recordService->getRecordById($id);
        $history = $this->recordService->getRecordMovements($id);
        $states = $this->stateService->getAll();
        $counterparties = $this->counterpartyService->getAll();
        $types = $this->typeService->getAll();
        $areas = $this->areaService->getAll();

        if (! $record) {
            return back()->with('error', 'Expediente no encontrado.');
        }

        return Inertia::render('Records/ViewRecord', [
            'title' => $record->number,
            'record' => $record,
            'date' => now()->format('Y-m-d'),
            'history' => $history,
            'states' => $states,
            'counterparties' => $counterparties,
            'types' => $types,
            'areas' => $areas,
        ]);
    }

    public function updaterecord(UpdateRecordRequest $request): RedirectResponse
    {
        $udpated = $this->recordService->updateRecord($request->validatedRecordData());
        if ($udpated) {
            return redirect()
                ->route('record.show', $udpated->id)
                ->with('success', 'Expediente modificado correctamente.');
        } else {
            return back()->with('error', 'No se pudo modificar el expediente. Inténtalo de nuevo.');
        }
    }

    public function addMovement(RecordMovementRequest $request)
    {
        $newMovementRecord = $this->recordService->addMovement($request->validatedNewData());
        if ($newMovementRecord) {
            return redirect()
                ->route('record.show', $newMovementRecord->id)
                ->with('success', 'Movimiento agregado correctamente.');
        } else {
            return back()->with('error', 'No se pudo agregar el movimiento. Inténtalo de nuevo.');
        }
    }

    public function deleteRecord(Request $request): RedirectResponse
    {
        $request = $request->validate(['record_id' => ['required', 'int', 'exists:records,id']]);
        $response = $this->recordService->deleteRecord($request['record_id']);
        if ($response == 1) {
            return redirect()
                ->route('records.index')
                ->with('success', 'Expediente eliminado correctamente.');
        } else {
            return back()->with('error', 'No se ha podido eliminar el expediente.');
        }
    }

    public function showAddRecord(): InertiaResponse
    {

        $states = $this->stateService->getAll();
        $counterparties = $this->counterpartyService->getAll();
        $types = $this->typeService->getAll();
        $areas = $this->areaService->getAll();

        return Inertia::render('Records/AddRecord', [
            'title' => 'Agregar Expediente',
            'date' => now()->format('Y-m-d'),
            'states' => $states,
            'counterparties' => $counterparties,
            'types' => $types,
            'areas' => $areas,
        ]);
    }

    public function addRecord(RecordRequest $request): RedirectResponse
    {
        $newRecord = $this->recordService->addRecord($request->validatedRecordData());
        if ($newRecord) {
            return back()->with('success', 'Expediente cargado correctamente.');
        } else {
            return back()->with('error', 'No se pudo cargar el expediente. Inténtalo de nuevo.');
        }
    }

    public function showAddRecords(): InertiaResponse
    {
        return Inertia::render('Records/AddRecordsFile', [
            'title' => 'Agregar Expediente',
        ]);
    }
}
