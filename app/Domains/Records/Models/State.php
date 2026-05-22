<?php

namespace App\Domains\Records\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'color'
    ];

    public $timestamps = false;


    public function records() : HasMany
    {
        return $this->hasMany(Record::class);
    }
}
