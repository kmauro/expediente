<?php

namespace App\Domains\Records\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Users\Models\User;

class RecordMovement extends Model
{
    protected $fillable = [
        'record_id',
        'to_area_id',
        'associated_doc',
        'moved_at',
        'user_id'
    ];

    public $timestamps = false;

    public function record(): BelongsTo
    {
        return $this->belongsTo(Record::class);
    }

    public function area() : BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
