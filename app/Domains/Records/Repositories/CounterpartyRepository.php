<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Counterparty;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CounterpartyRepository
{
    public function create(array $data): Counterparty
    {
        return Counterparty::create($data);
    }

    public function createGetId(array $data): int
    {
        $newCounterparty = Counterparty::create($data);

        return $newCounterparty->id;
    }

    public function getAll(): Collection
    {
        return Counterparty::all();
    }

    public function getById(int $id): Counterparty
    {
        return Counterparty::findOrFail($id);
    }

    public function getByNormalizedName(string $name)
    {
        return DB::table('counterparties')->where('normalized_name', $name)->get('id')->first();
    }
}
