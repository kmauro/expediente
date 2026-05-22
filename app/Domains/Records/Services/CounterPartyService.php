<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Models\Counterparty;
use App\Domains\Records\Repositories\CounterpartyRepository;
use App\Domains\Records\Services\NormalizeService;
use Illuminate\Support\Collection;

class CounterPartyService
{
    public function __construct(protected CounterpartyRepository $counterpartyRepository) {}

    public function getAll() : Collection
    {
        return $this->counterpartyRepository->getAll();
    }

    public function createGetId(string $name) : int
    {
        $newCounterpartyData = NormalizeService::normalizeName($name);
        return $this->counterpartyRepository->createGetId($newCounterpartyData);
    }

    public function create(array $data) : Counterparty
    {
        $newCounterpartyData = NormalizeService::normalizeName($data['name']);
        return $this->counterpartyRepository->create($newCounterpartyData);
    }
}
