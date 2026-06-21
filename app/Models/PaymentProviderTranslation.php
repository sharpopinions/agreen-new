<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProviderTranslation extends Model
{
    protected $fillable = ['payment_provider_id', 'language_id', 'name', 'description'];

    public function paymentProvider()
    {
        return $this->belongsTo(PaymentProvider::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
