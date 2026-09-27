<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'driver', 'name', 'settings', 'is_active'];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];


    public function syncLogs()
    {
        return $this->hasMany(IntegrationSyncLog::class);
    }
}
