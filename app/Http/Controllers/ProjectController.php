<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
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

        return new ProjectResource('success', 'Data Project Berhasil Ditambahkan!', $project);
    }

    public function show($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return new ProjectResource('error', 'Project not found!', null);
        }

        return new ProjectResource('success', 'Detail Data Project!', $project);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'string',
            'title' => 'string',
        ]);

        if ($validator->fails()) {
            return new ProjectResource('error', 'Validation Error', $validator->errors());
        }

        $project = Project::find($id);

        if (!$project) {
            return new ProjectResource('error', 'Project not found!', null);
        }

        if ($request->hasFile('image')) {
            Storage::delete('public/images/' . $project->image);

            $image = $request->file('image');
            $image->storeAs('public/images', $image->hashName());

            $project->image = $image->hashName();
        }

        $project->update($request->except('image'));

        return new ProjectResource('success', 'Data Project Berhasil Diubah!', $project);
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
        $project = Project::where('slug', $slug)->first();

        if (!$project) {
            return new ProjectResource('error', 'Project not found!', null);
        }

        return new ProjectResource('success', 'Detail Data Project!', $project);
    }
}
