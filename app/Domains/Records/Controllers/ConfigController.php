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
        $new = $stateService->createState($request->validatedStateData());
        if($new){
            return back()->with('success','Se ha creado el estado de forma exitosa.');
        }else{
            return back()->with('error', 'No se pudo crear el estado, intente nuevamente.');
        }
    }

    public function addType(TypeRequest $request, TypeService $typeService) : RedirectResponse
    {
        dd($request->validatedTypeData());
        $new = $typeService->createType($request->validatedTypeData());
        if($new){
            return back()->with('success','Se ha creado el objeto de forma exitosa.');
        }else{
            return back()->with('error', 'No se pudo crear el objeto, intente nuevamente.');
        }
    }

    public function addCounterParty(CounterpartyRequest $request, CounterPartyService $counterpartyService) : RedirectResponse
    {
        $new = $counterpartyService->createCounterparty($request->validatedCounterpartyData());
        if($new){
            return back()->with('success','Se ha creado el objeto de forma exitosa.');
        }else{
            return back()->with('error', 'No se pudo crear el objeto, intente nuevamente.');
        }
    }

    public function addArea(AreaRequest $request, AreaService $areaService) : RedirectResponse
    {
        $new = $areaService->createArea($request->validatedAreaData());
        if($new){
            return back()->with('success','Se ha creado el area de forma exitosa.');
        }else{
            return back()->with('error', 'No se pudo crear el area, intente nuevamente.');
        }
    }




}
