<?php

namespace App\Domains\Records\Controllers;

use App\Domains\Records\Requests\AreaRequest;
use App\Domains\Records\Requests\CounterpartyRequest;
use App\Http\Controllers\Controller;
use App\Domains\Records\Requests\StateRequest;
use App\Domains\Records\Requests\TypeRequest;
use App\Domains\Records\Services\AreaService;
use App\Domains\Records\Services\CounterPartyService;
use App\Domains\Records\Services\StateService;
use App\Domains\Records\Services\TypeService;
use Illuminate\Http\RedirectResponse;


class ConfigController extends Controller
{
    public function addState(StateRequest $request, StateService $stateService) : RedirectResponse
    {
        return $this->response($request, $stateService, 'estado');
    }

    public function addType(TypeRequest $request, TypeService $typeService) : RedirectResponse
    {
        return $this->response($request, $typeService, 'objeto');
    }

    public function addCounterParty(CounterpartyRequest $request, CounterPartyService $counterpartyService) : RedirectResponse
    {
        return $this->response($request, $counterpartyService, 'proveedor/programa');
    }

    public function addArea(AreaRequest $request, AreaService $areaService) : RedirectResponse
    {
        return $this->response($request, $areaService, 'area');
    }

    private function response($data, $service, string $message) : RedirectResponse
    {
        $new = $service->create($data->validatedNewData());
        if($new){
            return back()->with('success','Se ha creado el '. $message .' de forma exitosa.');
        }else{
            return back()->with('error', 'No se pudo crear el '. $message .', intente nuevamente.');
        }
    }




}
