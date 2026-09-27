<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class StockStatus extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'code', 'color', 'bg_color', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function translations()
    {
        return $this->hasMany(StockStatusTranslation::class);
    }
}
