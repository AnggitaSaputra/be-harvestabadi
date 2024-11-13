<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Models\ServiceImage;
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
            'slide*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return new ServiceResource('error', 'Validation Error', $validator->errors());
        }

        $imagePath = $request->file('image')->store('public/images');
        $imageName = basename($imagePath);

        $service = Service::create([
            'title' => $request->title,
            'image' => $imageName,
            'slug' => $request->slug,
            'content' => $request->content,
        ]);

        if ($request->hasFile('slide')) {
            foreach ($request->file('slide') as $slideFile) {
                $slidePath = $slideFile->store('public/images');
                $slideName = basename($slidePath);

                ServiceImage::create([
                    'service_id' => $service->id,
                    'image' => $slideName,
                ]);
            }
        }

        return new ServiceResource('success', 'Data Service Berhasil Ditambahkan!', $service);
    }

    public function show($id)
    {
        $service = Service::with('images')->find($id);

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
            'additional_images.*.id' => 'nullable|exists:service_images,id',  // Make id nullable
            'additional_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Make image nullable
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found',
            ], 404);
        }

        if ($request->hasFile('image')) {
            Storage::delete('public/images/' . $service->image);

            $image = $request->file('image');
            $image->storeAs('public/images', $image->hashName());

            $service->image = $image->hashName();
        }

        $service->save();
        if ($request->has('additional_images')) {
            foreach ($request->additional_images as $slideImageData) {
                // If id is provided and valid
                if (isset($slideImageData['id']) && $slideImageData['id']) {
                    $existingImage = ServiceImage::find($slideImageData['id']);

                    if ($existingImage) {
                        // If a new file is provided, delete the old image and store the new one
                        if (isset($slideImageData['image']) && $slideImageData['image']) {
                            Storage::delete('public/images/' . $existingImage->image);

                            $slideImage = $slideImageData['image'];
                            $slideImageName = $slideImage->hashName();
                            $slideImage->storeAs('public/images', $slideImageName);

                            // Update the image in the service_images table
                            $existingImage->update([
                                'image' => $slideImageName,
                            ]);
                        }
                    }
                } else {
                    // If the image ID is not provided, you may choose to create a new image
                    if (isset($slideImageData['image'])) {
                        $slideImage = $slideImageData['image'];
                        $slideImageName = $slideImage->hashName();
                        $slideImage->storeAs('public/images', $slideImageName);

                        // Add the new image to the service_images table
                        $service->images()->create([
                            'image' => $slideImageName,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Service updated successfully',
            'data' => $service->load('images'), // Load related images
        ]);
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
