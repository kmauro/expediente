<?php

namespace App\Domains\Records\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name',
        'normalized_name'
    ];

    public $timestamps = false;

    public function recordMovement(): HasMany
    {
        return $this->hasMany(RecordMovement::class);
    }


}
