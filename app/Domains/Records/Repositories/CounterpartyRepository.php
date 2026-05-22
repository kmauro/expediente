<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Counterparty;
use Illuminate\Support\Collection;

class CounterpartyRepository
{

    public function getAll(): Collection
    {
        return Counterparty::all();
    }

    public function createGetId(array $data) : int
    {
        $newCounterparty =  Counterparty::create($data);
        return $newCounterparty->id;
    }

    public function create(array $data) : Counterparty
    {
        return Counterparty::create($data);
    }
}
