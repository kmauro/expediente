<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Area;
use Illuminate\Support\Collection;

class AreaRepository
{
    public function getAll(): Collection
    {
        return Area::all();
    }

    public function createGetId(array $data) : int
    {
        $newArea =  Area::create($data);
        return $newArea->id;
    }

    public function create(array $data) : Area
    {
        return Area::create($data);
    }
}
