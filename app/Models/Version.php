<?php

namespace App\Models;

use Database\Factories\VersionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    /** @use HasFactory<VersionFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function scopeDefaultSelect(Builder $query): void
    {
        $query->select(['key', 'value']);
    }
}
