<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Area;
use App\Domains\Records\Repositories\AreaRepository;
use App\Domains\Records\Services\NormalizeService;
use Illuminate\Support\Collection;

class AreaService
{
    public function __construct(protected AreaRepository $areaRepository) {}

    public function getAll() : Collection
    {
        return $this->areaRepository->getAll();
    }

    public function createGetId(string $name) : int
    {
        $newAreaData = NormalizeService::normalizeName($name);
        return $this->areaRepository->createGetId($newAreaData);
    }

    public function create(array $data) : Area
    {
        $newAreaData = NormalizeService::normalizeName($data['name']);
        return $this->areaRepository->create($newAreaData);
    }
}
