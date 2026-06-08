<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Type;
use App\Domains\Records\Repositories\TypeRepository;
use Illuminate\Support\Collection;

class TypeService
{
    public function __construct(protected TypeRepository $typeRepository) {}

    public function create(array $data): Type
    {
        $newTypeData = NormalizeService::normalizeName($data['name']);

        return $this->typeRepository->create($newTypeData);
    }

    public function createGetId(string $name): int
    {
        $newTypeData = NormalizeService::normalizeName($name);

        return $this->typeRepository->createGetId($newTypeData);
    }

    public function findOrCreateGetId(string $name): int
    {
        $id = null;
        $newTypeData = NormalizeService::normalizeName($name);
        $founded = $this->typeRepository->getByNormalizedName($newTypeData['normalized_name']);
        if(empty($founded)){
            $id = $this->typeRepository->createGetId($newTypeData);
        }else{
            $id = $founded->id;
        }
        return $id;
    }

    public function getAll(): Collection
    {
        return $this->typeRepository->getAll();
    }

    public function getById(int $id): Type
    {
        return $this->typeRepository->getById($id);
    }
}
