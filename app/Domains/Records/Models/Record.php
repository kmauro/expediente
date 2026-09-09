<?php

namespace App\Domains\Records\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Record extends Model
{
    protected $fillable = [
        'number',
        'title',
        'state_id',
        'type_id',
        'counterparty_id',
        'last_movement_id',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function lastMovement(): BelongsTo
    {
        return $this->belongsTo(RecordMovement::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(RecordMovement::class);
    }
}
