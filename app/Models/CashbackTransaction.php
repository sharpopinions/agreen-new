<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

/** Рух кешбеку: нарахування (amount > 0) або списання (amount < 0). */
class CashbackTransaction extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'user_id', 'order_id', 'amount', 'description'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
