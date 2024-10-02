<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getWhatsAppNumber()
    {
        $whatsAppNumber = Setting::getWhatsAppNumber();

        if ($whatsAppNumber) {
            return new SettingResource(200, 'WhatsApp number retrieved successfully', [
                'whatsAppNumber' => $whatsAppNumber,
            ]);
        } else {
            return new SettingResource(404, 'WhatsApp number not found 2', null);
        }
    }

    public function saveWhatsAppNumber(Request $request)
    {
        $request->validate([
            'whatsAppNumber' => 'required|string',
        ]);

        Setting::saveWhatsAppNumber($request->whatsAppNumber);

        return new SettingResource(200, 'WhatsApp number updated successfully', [
            'whatsAppNumber' => $request->whatsAppNumber,
        ]);
    }
}
