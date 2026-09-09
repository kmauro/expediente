<?php

namespace App\Domains\Files\Controllers;

use App\Domains\Files\Requests\ImportFileRequest;
use App\Domains\Files\Services\FileService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
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
}
