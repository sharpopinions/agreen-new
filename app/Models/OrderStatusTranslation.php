<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusTranslation extends Model
{
    protected $fillable = ['order_status_id', 'language_id', 'name'];

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
