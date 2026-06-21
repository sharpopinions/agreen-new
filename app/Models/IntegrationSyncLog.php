<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSyncLog extends Model
{
    protected $fillable = ['integration_id', 'status', 'message', 'payload', 'started_at', 'finished_at'];

    protected $casts = [
        'payload'     => 'array',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }
}
