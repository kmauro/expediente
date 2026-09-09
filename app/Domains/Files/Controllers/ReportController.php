<?php

namespace App\Domains\Files\Controllers;

use App\Domains\Files\Requests\DownloadReportRequest;
use App\Domains\Files\Requests\ReportRequest;
use App\Domains\Files\Services\ReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index(ReportRequest $request): InertiaResponse
    {
        $requestedDate = $request->validatedDate();
        $date = $requestedDate ?? now()->format('Y-m-d');
        $records = $requestedDate === null ? [] : $this->reportService->search($requestedDate);

        return Inertia::render('Files/GenerateReport', [
            'title' => 'Generar Reporte',
            'date' => $date,
            'records' => $records,
            'hasSearched' => $requestedDate !== null,
            'searchUrl' => route('report.generate'),
            'downloadUrl' => $records === []
                ? null
                : route('report.generate.excel', ['date' => $date]),
        ]);
    }

    public function download(DownloadReportRequest $request): BinaryFileResponse|RedirectResponse
    {
        $date = $request->validatedDate();

        try {
            $report = $this->reportService->download($date);
        } catch (Throwable $exception) {
            report($exception);

            return to_route('report.generate', ['date' => $date])
                ->with('error', 'No se pudo generar el reporte. Inténtalo de nuevo.');
        }

        if ($report === null) {
            return to_route('report.generate', ['date' => $date])
                ->with('error', 'No hay expedientes para descargar en la fecha seleccionada.');
        }

        return $report;
    }
}
