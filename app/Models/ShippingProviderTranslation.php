<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingProviderTranslation extends Model
{
    protected $fillable = ['shipping_provider_id', 'language_id', 'name', 'description'];

    public function shippingProvider()
    {
        return $this->belongsTo(ShippingProvider::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
