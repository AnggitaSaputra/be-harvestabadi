<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feature;
use App\Http\Resources\FeatureResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::all();
        return new FeatureResource(true, 'List Data Features', $features);
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
            return new FeatureResource(false, 'Validation Error', $validator->errors());
        }

        $image = $request->file('image');
        $imagePath = Storage::putFileAs('public/images', $image, $image->hashName());

        $feature = Feature::create([
            'title' => $request->title,
            'image' => basename($imagePath),
            'slug' => $request->slug,
            'content' => $request->content,
        ]);

        return new FeatureResource(true, 'Data Feature Berhasil Ditambahkan!', $feature);
    }

    public function show($id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return new FeatureResource(false, 'Feature not found!', null);
        }

        return new FeatureResource(true, 'Detail Data Feature!', $feature);
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
            return new FeatureResource(false, 'Validation Error', $validator->errors());
        }

        $feature = Feature::find($id);

        if (!$feature) {
            return new FeatureResource(false, 'Feature not found!', null);
        }

        if ($request->hasFile('image')) {
            Storage::delete('public/images/' . $feature->image);

            $image = $request->file('image');
            $image->storeAs('public/images', $image->hashName());

            $feature->image = $image->hashName();
        }

        $feature->update($request->except('image'));
        $feature->save();

        return new FeatureResource(true, 'Data Feature Berhasil Diubah!', $feature);
    }

    public function destroy($id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return new FeatureResource(false, 'Feature not found!', null);
        }

        $feature->delete();

        return new FeatureResource(true, 'Data Feature Berhasil Dihapus!', null);
    }

    public function getAllFeature()
    {
        $features = Feature::all();
        return new FeatureResource(true, 'List Data Features', $features);
    }

    public function getFeatureBySlug($slug)
    {
        $feature = Feature::where('slug', $slug)->first();

        if (!$feature) {
            return new FeatureResource(false, 'Feature not found!', null);
        }

        return new FeatureResource(true, 'Detail Data Feature!', $feature);
    }
}
