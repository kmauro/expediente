<?php

namespace App\Domains\Records\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    protected $fillable = [
        'name',
        'normalized_name'
    ];

    public $timestamps = false;

    public function record(): HasMany
    {
        return $this->hasMany(Record::class);
    }
}
