<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'domain', 'settings', 'plan', 'is_active'];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function languages() {
        return $this->hasMany(Language::class);
    }

    public function currencies() {
        return $this->hasMany(Currency::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function integrations()
    {
        return $this->hasMany(Integration::class);
    }

    public function shippingProviders()
    {
        return $this->hasMany(ShippingProvider::class);
    }

    public function paymentProviders()
    {
        return $this->hasMany(PaymentProvider::class);
    }
}
