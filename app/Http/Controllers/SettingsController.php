<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function getFeaturedImage()
    {
        $featuredImage = Setting::getFeaturedImage();

        if ($featuredImage) {
            return new SettingResource(200, 'Featured image retrieved successfully', [
                'featuredImage' => $featuredImage,
            ]);
        } else {
            return new SettingResource(404, 'Featured image not found', null);
        }
    }

    public function saveFeaturedImage(Request $request)
    {
        $request->validate([
            'featuredImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $existingImage = Setting::getFeaturedImage();
        if ($existingImage) {
            Storage::disk('public')->delete('images/' . $existingImage);
        }

        $fileName = $request->file('featuredImage')->hashName();
        $request->file('featuredImage')->storeAs('images', $fileName, 'public');

        Setting::saveFeaturedImage($fileName);

        return new SettingResource(200, 'Featured image updated successfully', [
            'featuredImage' => $fileName,
        ]);
    }
}
