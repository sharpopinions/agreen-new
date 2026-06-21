<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $fillable = ['store_id', 'driver', 'name', 'settings', 'is_active'];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(IntegrationSyncLog::class);
    }
}
