<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVideo extends Model
{
    protected $fillable = ['product_id', 'youtube_id', 'sort_order'];

    /**
     * ID ролика з будь-якого посилання YouTube (watch?v=, youtu.be/, shorts/, embed/) або сам ID.
     */
    public static function parseYoutubeId(?string $input): ?string
    {
        $input = trim((string) $input);
        if (preg_match('~^[\w-]{11}$~', $input)) {
            return $input;
        }
        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{11})~', $input, $m)) {
            return $m[1];
        }

        return null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
