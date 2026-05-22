<?php

namespace App\Domains\Records\Services;

use App\Domains\Records\Repositories\StateRepository;
use Illuminate\Support\Collection;
use App\Domains\Records\Models\State;

class StateService
{
    public function __construct(protected StateRepository $stateRepository) {}

    public function create(array $data) : State
    {
        return $this->stateRepository->create($data);
    }

    public function getAll() : Collection
    {
        return $this->stateRepository->getAll();
    }
}
