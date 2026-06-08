<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\Type;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TypeRepository
{
    public function create(array $data): Type
    {
        return Type::create($data);
    }

    public function createGetId(array $data): int
    {
        $newType = Type::create($data);

        return $newType->id;
    }

    public function getAll(): Collection
    {
        return Type::all();
    }

    public function getById(int $id): Type
    {
        return Type::findOrFail($id);
    }

    public function getByNormalizedName(string $name)
    {
        return DB::table('types')->where('normalized_name', $name)->get('id')->first();
    }
}
