<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Area;
use App\Domains\Records\Repositories\AreaRepository;
use App\Domains\Records\Services\NormalizeService;
use Illuminate\Support\Collection;

class AreaService
{
    public function __construct(protected AreaRepository $areaRepository) {}

    public function create(array $data) : Area
    {
        $newAreaData = NormalizeService::normalizeName($data['name']);
        return $this->areaRepository->create($newAreaData);
    }

    public function createGetId(string $name) : int
    {
        $newAreaData = NormalizeService::normalizeName($name);
        return $this->areaRepository->createGetId($newAreaData);
    }

    public function findOrCreateGetId(string $name): int
    {
        $id = null;
        $newAreaData = NormalizeService::normalizeName($name);
        $founded = $this->areaRepository->getByNormalizedName($newAreaData['normalized_name']);
        if(empty($founded)){
            $id = $this->areaRepository->createGetId($newAreaData);
        }else{
            $id = $founded->id;
        }
        return $id;
    }

    public function getAll() : Collection
    {
        return $this->areaRepository->getAll();
    }
}
