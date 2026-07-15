<?php

namespace App\Domains\Files\Controllers;

use App\Domains\Files\Requests\DownloadReportRequest;
use App\Domains\Files\Requests\ImportFileRequest;
use App\Domains\Files\Services\FileService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class FileController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    public function showUploadFile(): InertiaResponse
    {
        return Inertia::render('Files/UploadFile', [
            'title' => 'Agregar Expediente',
        ]);
    }

    public function uploadFile(ImportFileRequest $request): RedirectResponse
    {
        $validated = $request->validatedNewData();

        try {
            $result = $this->fileService->processExcelFile($validated['file']);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'No se pudo leer el archivo Excel. Verifica que el archivo no este vacio o dañado.');
        }

        if ($result['total_rows'] === 0) {
            return back()->with('error', 'No se encontraron filas para importar en el archivo.');
        }

        $message = "Archivo procesado correctamente. Expedientes detectados: {$result['total_rows']}.";

        if ($result['skipped_rows'] > 0) {
            $message .= " Filas omitidas por no contener un expediente válido: {$result['skipped_rows']}.";
        }

        return redirect()
            ->route('file.add')
            ->with('success', $message);
    }

    public function showGenerateReport(Request $request): InertiaResponse
    {
        if ($request->has('records')) {
            $records = $request->input('records');
        } else {
            $records = [];
        }

        return Inertia::render('Files/GenerateReport', [
            'title' => 'Generar Reporte',
            'date' => now()->format('Y-m-d'),
            'records' => $records,
        ]);
    }

    public function searchReport(Request $request): RedirectResponse
    {
        $records = $this->fileService->searchReport($request->all());

        return redirect()
            ->route('report.generate', ['records' => $records]);
    }

    public function downloadExcelReport(DownloadReportRequest $request): BinaryFileResponse|RedirectResponse
    {
        $validated = $request->validatedNewData();
        $report = $this->fileService->generateExcelReport($validated['records']);
        if ($report) {
            return $report;
        }

        return back()->with('error', 'No se pudo generar el reporte. Intentalo de nuevo.');
    }
}
