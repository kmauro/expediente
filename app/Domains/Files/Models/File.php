<?php

namespace App\Domains\Files\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'date',
        'number',
        'title',
        'history',
        'last_movement',
        'type',
        'counterparty',
    ];
}
