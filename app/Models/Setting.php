<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function getWhatsAppNumber()
    {
        return self::where('key', 'whatsAppNumber')->first()->value ?? null;
    }

    public static function saveWhatsAppNumber($number)
    {
        return self::updateOrCreate(['key' => 'whatsAppNumber'], ['value' => $number]);
    }

    public static function getFeaturedImage()
    {
        return self::where('key', 'featured_image')->first()->value ?? null;
    }

    public static function saveFeaturedImage($imagePath)
    {
        return self::updateOrCreate(['key' => 'featured_image'], ['value' => $imagePath]);
    }
}
