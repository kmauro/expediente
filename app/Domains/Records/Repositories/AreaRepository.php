<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Area;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AreaRepository
{

    public function create(array $data) : Area
    {
        return Area::create($data);
    }

    public function createGetId(array $data) : int
    {
        $newArea =  Area::create($data);
        return $newArea->id;
    }

    public function getByNormalizedName(string $name)
    {
        return DB::table('areas')->where('normalized_name', $name)->get('id')->first();
    }

    public function getAll(): Collection
    {
        return Area::all();
    }
}
