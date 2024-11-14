<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Models\ProjectImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return new ProjectResource('success', 'List Data Projects', $projects);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'required|string',
            'title' => 'required|string',
            'slide*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return new ProjectResource('error', 'Validation Error', $validator->errors());
        }

        $image = $request->file('image');
        $imagePath = Storage::putFileAs('public/images', $image, $image->hashName());

        $project = Project::create([
            'link' => $request->link,
            'image' => basename($imagePath),
            'slug' => $request->slug,
            'title' => $request->title,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $slideKey = "slide$i";
            if ($request->hasFile($slideKey)) {
                $slideFile = $request->file($slideKey);
                $slidePath = Storage::putFileAs('public/images', $slideFile, $slideFile->hashName());

                ProjectImage::create([
                    'project_id' => $project->id,
                    'image' => basename($slidePath),
                ]);
            }
        }

        return new ProjectResource('success', 'Data Project Berhasil Ditambahkan!', $project);
    }

    public function show($id)
    {
        $project = Project::with('images')->find($id);

        if (!$project) {
            return new ProjectResource('error', 'Project not found!', null);
        }

        return new ProjectResource('success', 'Detail Data Project!', $project);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|required',
            'slug' => 'string|required',
            'link' => 'string|nullable',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'additional_images.*.id' => 'nullable|exists:project_images,id',  // Make id nullable
            'additional_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Make image nullable
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the project by ID
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        // Update the basic project fields
        $project->title = $request->title;
        $project->slug = $request->slug;
        $project->link = $request->link;

        // Handle the cover image (as before)
        if ($request->hasFile('image')) {
            if ($project->image) {
                // Delete the old cover image from storage
                Storage::delete('public/images/' . $project->image);
            }

            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('public/images', $imageName);
            $project->image = $imageName;
        }

        $project->save();

        // Handle additional images
        if ($request->has('additional_images')) {
            foreach ($request->additional_images as $slideImageData) {
                // If id is provided and valid
                if (isset($slideImageData['id']) && $slideImageData['id']) {
                    $existingImage = ProjectImage::find($slideImageData['id']);

                    if ($existingImage) {
                        // If a new file is provided, delete the old image and store the new one
                        if (isset($slideImageData['image']) && $slideImageData['image']) {
                            Storage::delete('public/images/' . $existingImage->image);

                            $slideImage = $slideImageData['image'];
                            $slideImageName = $slideImage->hashName();
                            $slideImage->storeAs('public/images', $slideImageName);

                            // Update the image in the project_images table
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

                        // Add the new image to the project_images table
                        $project->images()->create([
                            'image' => $slideImageName,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully',
            'data' => $project->load('images'), // Load related images
        ]);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return new ProjectResource('error', 'Project not found!', null);
        }

        $project->delete();

        return new ProjectResource('success', 'Data Project Berhasil Dihapus!', null);
    }

    public function getAllProject()
    {
        $projects = Project::all();
        return new ProjectResource('success', 'List Data Projects', $projects);
    }

    public function getProjectBySlug($slug)
    {
        $project = Project::with('images')->where('slug', $slug)->first();

        if (!$project) {
            return new ProjectResource('error', 'Project not found!', null);
        }

        return new ProjectResource('success', 'Detail Data Project!', $project);
    }
}
