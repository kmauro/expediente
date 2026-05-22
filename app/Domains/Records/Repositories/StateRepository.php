<?php

namespace App\Domains\Records\Repositories;

use App\Domains\Records\Models\State;
use Illuminate\Support\Collection;

class StateRepository
{

    public function create(array $data) : State
    {
        $new = State::create($data);
        return $new;
    }

    public function getAll(): Collection
    {
        return State::all();
    }
}
