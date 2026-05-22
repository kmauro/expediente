<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Type;
use App\Domains\Records\Repositories\TypeRepository;
use App\Domains\Records\Services\NormalizeService;
use Illuminate\Support\Collection;

class TypeService
{
    public function __construct(protected TypeRepository $typeRepository) {}

    public function getAll() : Collection
    {
        return $this->typeRepository->getAll();
    }

    public function createGetId(array $data) : int
    {
        $newTypeData = NormalizeService::normalizeName($data['name']);
        return $this->typeRepository->createGetId($newTypeData);
    }

    public function createType(string $name) : Type
    {
        $newTypeData = NormalizeService::normalizeName($name);
        return $this->typeRepository->create($newTypeData);
    }
}
