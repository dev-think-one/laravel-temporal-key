<?php

namespace TemporalKey\Models;

use Illuminate\Database\Eloquent\Model;

class TemporalKeyStore extends Model
{
    protected $guarded = [];

    protected $casts = [
        'valid_until' => 'datetime',
        'meta'        => 'array',
    ];

    public function getTable():string
    {
        return config('temporal-key.tables.temporal_keys');
    }
}
