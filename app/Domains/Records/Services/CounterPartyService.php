<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Counterparty;
use App\Domains\Records\Repositories\CounterpartyRepository;
use Illuminate\Support\Collection;

class CounterPartyService
{
    public function __construct(protected CounterpartyRepository $counterpartyRepository) {}

    public function create(array $data): Counterparty
    {
        $newCounterpartyData = NormalizeService::normalizeName($data['name']);

        return $this->counterpartyRepository->create($newCounterpartyData);
    }

    public function createGetId(string $name): int
    {
        $newCounterpartyData = NormalizeService::normalizeName($name);

        return $this->counterpartyRepository->createGetId($newCounterpartyData);
    }

    public function findOrCreateGetId(string $name): int
    {
        $id = null;
        $newCounterpartyData = NormalizeService::normalizeName($name);
        $founded = $this->counterpartyRepository->getByNormalizedName($newCounterpartyData['normalized_name']);
        if(empty($founded)){
            $id = $this->counterpartyRepository->createGetId($newCounterpartyData);
        }else{
            $id = $founded->id;
        }
        return $id;
    }

    public function getAll(): Collection
    {
        return $this->counterpartyRepository->getAll();
    }

    public function getById(int $id): Counterparty
    {
        return $this->counterpartyRepository->getById($id);
    }
}
