<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Type;
use App\Domains\Records\Repositories\TypeRepository;
use App\Domains\Records\Services\NormalizeService;
use Illuminate\Support\Collection;

class TypeService
{
    public function __construct(protected TypeRepository $typeRepository) {}

    public function create(array $data) : Type
    {
        $newTypeData = NormalizeService::normalizeName($data['name']);
        return $this->typeRepository->create($newTypeData);
    }

    public function createGetId(string $name) : int
    {
        $newTypeData = NormalizeService::normalizeName($name);
        return $this->typeRepository->createGetId($newTypeData);
    }

    public function getAll() : Collection
    {
        return $this->typeRepository->getAll();
    }
}
