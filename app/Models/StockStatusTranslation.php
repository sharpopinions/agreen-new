<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockStatusTranslation extends Model
{
    protected $fillable = ['stock_status_id', 'language_id', 'name'];

    public function stockStatus()
    {
        return $this->belongsTo(StockStatus::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
