<?php

use App\Domains\Files\Services\ExcelReportService;
use App\Domains\Files\Services\ReportService;
use App\Domains\Records\Models\Record;
use App\Domains\Records\Models\RecordMovement;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

beforeEach(function () {
    $roleId = DB::table('roles')->insertGetId(['role_name' => 'admin']);
    $this->stateId = DB::table('states')->insertGetId([
        'name' => 'En buzón',
        'color' => 'teal',
    ]);
    $this->typeId = DB::table('types')->insertGetId([
        'name' => 'Contratación',
        'normalized_name' => 'contratacion',
    ]);
    $this->counterpartyId = DB::table('counterparties')->insertGetId([
        'name' => 'Proveedor de prueba',
        'normalized_name' => 'proveedor de prueba',
    ]);
    $this->areaId = DB::table('areas')->insertGetId([
        'name' => 'Mesa de Entradas',
        'normalized_name' => 'mesa de entradas',
    ]);
    $this->user = User::query()->create([
        'name' => 'Reportes',
        'surname' => 'Pruebas',
        'username' => 'reportes',
        'email' => 'reportes@example.test',
        'password' => 'password',
        'phone' => null,
        'role_id' => $roleId,
    ]);

    $this->createReportRecord = function (
        array $recordOverrides = [],
        array $movements = [],
    ): array {
        $record = Record::query()->create(array_merge([
            'number' => 'EX-'.Str::uuid(),
            'title' => 'Expediente de prueba',
            'state_id' => $this->stateId,
            'type_id' => $this->typeId,
            'counterparty_id' => $this->counterpartyId,
            'last_movement_id' => null,
            'created_at' => '2026-06-01',
            'updated_at' => '2026-06-30',
        ], $recordOverrides));

        $movements = $movements === [] ? [
            ['moved_at' => '2026-06-29', 'associated_doc' => 'Primer movimiento'],
            ['moved_at' => '2026-06-30', 'associated_doc' => 'Último movimiento'],
        ] : $movements;

        $lastMovement = null;

        foreach ($movements as $movementData) {
            $lastMovement = RecordMovement::query()->create([
                'record_id' => $record->id,
                'to_area_id' => $this->areaId,
                'associated_doc' => $movementData['associated_doc'],
                'moved_at' => $movementData['moved_at'],
                'user_id' => $this->user->id,
            ]);
        }

        $record->update(['last_movement_id' => $lastMovement?->id]);

        return ['record' => $record->fresh(), 'last_movement' => $lastMovement];
    };
});

test('guests are redirected from report pages', function () {
    $this->get(route('report.generate'))
        ->assertRedirect(route('login'));

    $this->get(route('report.generate.excel', ['date' => '2026-06-30']))
        ->assertRedirect(route('login'));
});

test('the report page starts without running a query', function () {
    $this->actingAs($this->user)
        ->get(route('report.generate'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('Files/GenerateReport')
            ->where('date', now()->format('Y-m-d'))
            ->where('records', [])
            ->where('hasSearched', false)
            ->where('downloadUrl', null)
            ->etc());
});

test('it returns only records from the selected date with the correct last movement', function () {
    ($this->createReportRecord)([
        'title' => 'Otro expediente',
        'updated_at' => '2025-01-09',
    ], [
        ['moved_at' => '2025-01-09', 'associated_doc' => 'Movimiento ajeno'],
    ]);

    $fixture = ($this->createReportRecord)(['title' => 'Expediente seleccionado']);

    $this->actingAs($this->user)
        ->get(route('report.generate', ['date' => '2026-06-30']))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('Files/GenerateReport')
            ->where('date', '2026-06-30')
            ->where('hasSearched', true)
            ->has('records', 1)
            ->where('records.0.id', $fixture['record']->id)
            ->where('records.0.title', 'Expediente seleccionado')
            ->where('records.0.last_movement', '2026-06-30 Enviado a Mesa de Entradas: Último movimiento')
            ->where('records.0.history.0.associated_doc', 'Primer movimiento')
            ->where('records.0.history.1.associated_doc', 'Último movimiento')
            ->etc());
});

test('report dates are validated', function (string $routeName, array $parameters) {
    $this->actingAs($this->user)
        ->get(route($routeName, $parameters))
        ->assertSessionHasErrors('date');
})->with([
    'invalid search date' => ['report.generate', ['date' => '30-06-2026']],
    'missing download date' => ['report.generate.excel', []],
    'invalid download date' => ['report.generate.excel', ['date' => 'not-a-date']],
]);

test('the download rebuilds trusted report data on the server', function () {
    ($this->createReportRecord)(['title' => 'Título confiable']);
    Excel::fake();

    $this->actingAs($this->user)
        ->get(route('report.generate.excel', [
            'date' => '2026-06-30',
            'records' => [['title' => '=HYPERLINK("https://example.test")']],
        ]))
        ->assertSuccessful();

    Excel::assertDownloaded(
        'reporte_expedientes.xlsx',
        fn (ExcelReportService $export): bool => $export->array()[0]['title'] === 'Título confiable',
    );
});

test('it returns an xlsx download with the expected filename', function () {
    ($this->createReportRecord)();

    $this->actingAs($this->user)
        ->get(route('report.generate.excel', ['date' => '2026-06-30']))
        ->assertDownload('reporte_expedientes.xlsx');
});

test('an empty report redirects with a visible error', function () {
    Excel::fake();

    $this->actingAs($this->user)
        ->get(route('report.generate.excel', ['date' => '2026-01-01']))
        ->assertRedirect(route('report.generate', ['date' => '2026-01-01']))
        ->assertSessionHas('error', 'No hay expedientes para descargar en la fecha seleccionada.');
});

test('report query count stays constant as records increase', function () {
    ($this->createReportRecord)();
    $service = $this->app->make(ReportService::class);

    DB::enableQueryLog();
    DB::flushQueryLog();
    $service->search('2026-06-30');
    $singleRecordQueryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    ($this->createReportRecord)(['title' => 'Segundo expediente']);
    ($this->createReportRecord)(['title' => 'Tercer expediente']);

    DB::enableQueryLog();
    DB::flushQueryLog();
    $service->search('2026-06-30');
    $multipleRecordQueryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($multipleRecordQueryCount)
        ->toBe($singleRecordQueryCount)
        ->toBeLessThanOrEqual(7);
});

test('unexpected export failures are reported and shown to the user', function () {
    Exceptions::fake();
    $exception = new RuntimeException('No se pudo escribir el archivo.');
    $reportService = Mockery::mock(ReportService::class);
    $reportService->shouldReceive('download')
        ->once()
        ->with('2026-06-30')
        ->andThrow($exception);
    $this->app->instance(ReportService::class, $reportService);

    $this->actingAs($this->user)
        ->get(route('report.generate.excel', ['date' => '2026-06-30']))
        ->assertRedirect(route('report.generate', ['date' => '2026-06-30']))
        ->assertSessionHas('error', 'No se pudo generar el reporte. Inténtalo de nuevo.');

    Exceptions::assertReported(RuntimeException::class);
});
