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
}
