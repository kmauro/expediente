<?php

namespace App\Domains\Files\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class FileImport implements ToCollection
{
    public function collection(Collection $collection): void {}
}
