<?php

namespace App\Domains\Records\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domains\Records\Requests\RecordRequest;
use App\Domains\Records\Services\RecordService;
use App\Domains\Records\Services\StateService;
use App\Domains\Records\Services\CounterPartyService;
use App\Domains\Records\Services\TypeService;
use App\Domains\Records\Services\AreaService;
use Inertia\Inertia;

use Inertia\Response as InertiaResponse;

class RecordController extends Controller
{
    public function __construct(
        protected AreaService $areaService,
        protected StateService $stateService,
        protected CounterPartyService $counterpartyService,
        protected TypeService $typeService)
    {}

    public function showAddRecords() : InertiaResponse
    {
        return Inertia::render('Records/AddRecordsFile', [
            'title' => 'Agregar Expediente',
        ]);
    }

    public function index(RecordService $recordService) : InertiaResponse
    {
        $path = public_path('\json\mockdata.json');
        $contenido = file_get_contents($path);
        $datos = json_decode($contenido, true);
        $records = $recordService->getAllRecords();
        return Inertia::render('Records/index', [
            'title' => 'Lista de Expedientes',
            'data' => $records,
        ]);
    }

    public function showRecord(String $id, RecordService $recordService)
    {
        $record = $recordService->getRecordById($id);
        $history = $record->recordMovement()->with('area')->orderBy('moved_at', 'desc')->get();
        $states = $this->stateService->getAll();
        $counterparties = $this->counterpartyService->getAll();
        $types = $this->typeService->getAll();
        $areas = $this->areaService->getAll();

        if(!$record) {
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



    public function showGenerateReport() : InertiaResponse
    {
        return Inertia::render('Records/GenerateReport', [
            'title' => 'Generar Reporte',
            'date' => now()->format('Y-m-d'),
        ]);
    }

    public function downloadGeneratedReport(Request $request) : RedirectResponse
    {
        $user = Auth::user();
        $user2=null;
        if ($user) {
            return back()->with('success', 'Reporte generado correctamente, descarga iniciada.');
        }else {
            return back()->with('error', 'No se pudo generar el reporte. Inténtalo de nuevo.');
        }
    }





    public function addRecords(Request $request) : RedirectResponse
    {
        $user = Auth::user();
        $user2=null;
        if ($user) {
            return back()->with('success', 'Archivo cargado correctamente.');
        }else {
            return back()->with('error', 'No se pudo cargar el archivo. Inténtalo de nuevo.');
        }
    }

    public function updaterecord(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user2 = null;
        if ($user) {
            return back()->with('success', 'Expediente modificado correctamente.');
        }else {
            return back()->with('error', 'No se pudo modificar el expediente. Inténtalo de nuevo.');
        }
    }




    public function showAddRecord() : InertiaResponse
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


    public function addRecord(RecordRequest $request, RecordService $recordService) : RedirectResponse
    {
        $newRecord = $recordService->addRecord($request->validatedRecordData());
        if ($newRecord) {
            return back()->with('success', 'Expediente cargado correctamente.');
        }else {
            return back()->with('error', 'No se pudo cargar el expediente. Inténtalo de nuevo.');
        }
    }
}
