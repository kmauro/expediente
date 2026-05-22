<?php

namespace App\Domains\Records\Services;

 use Illuminate\Support\Str;

class NormalizeService
{


    public static function normalizeName(string $name): array
    {
        $normalizedName = Str::of($name)
            ->ascii() //quita acentos
            ->lower() //quita mayusculas
            ->squish() //quita espacios extras
            ->toString(); //convierte a string

        return [
            'name' => $name,
            'normalized_name' => $normalizedName
        ];
    }
}
