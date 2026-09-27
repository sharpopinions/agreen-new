<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'driver', 'settings', 'sort_order', 'is_active'];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];


    public function translations()
    {
        return $this->hasMany(PaymentProviderTranslation::class);
    }
}
