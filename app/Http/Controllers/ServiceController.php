<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return new ServiceResource('success', 'List Data Services', $services);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new ServiceResource('error', 'Validation Error', $validator->errors());
        }

        $image = $request->file('image');
        $imagePath = Storage::putFileAs('public/images', $image, $image->hashName());

        $service = Service::create([
            'title' => $request->title,
            'image' => basename($imagePath),
            'slug' => $request->slug,
            'content' => $request->content,
        ]);

        return new ServiceResource('success', 'Data Service Berhasil Ditambahkan!', $service);
    }

    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return new ServiceResource('error', 'Service not found!', null);
        }

        return new ServiceResource('success', 'Detail Data Service!', $service);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'string',
            'content' => 'string',
        ]);

        if ($validator->fails()) {
            return new ServiceResource('error', 'Validation Error', $validator->errors());
        }

        $service = Service::find($id);

        if (!$service) {
            return new ServiceResource('error', 'Service not found!', null);
        }

        if ($request->hasFile('image')) {
            Storage::delete('public/images/' . $service->image);

            $image = $request->file('image');
            $image->storeAs('public/images', $image->hashName());

            $service->image = $image->hashName();
        }

        $service->update($request->except('image'));
        $service->save();

        return new ServiceResource('success', 'Data Service Berhasil Diubah!', $service);
    }

    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return new ServiceResource('error', 'Service not found!', null);
        }

        $service->delete();

        return new ServiceResource('success', 'Data Service Berhasil Dihapus!', null);
    }

    public function getAllService()
    {
        $services = Service::all();
        return new ServiceResource('success', 'List Data Services', $services);
    }

    public function getServiceBySlug($slug)
    {
        $service = Service::where('slug', $slug)->first();

        if (!$service) {
            return new ServiceResource('error', 'Service not found!', null);
        }

        return new ServiceResource('success', 'Detail Data Service!', $service);
    }
}
