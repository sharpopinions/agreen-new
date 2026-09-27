<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'code', 'name', 'symbol', 'rate', 'is_default', 'is_active'];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }
}
