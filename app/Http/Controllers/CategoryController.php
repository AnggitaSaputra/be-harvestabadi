<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return new CategoryResource('success', 'List Data Categories', $categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($request->all());

        return new CategoryResource('success', 'Data Kategori Berhasil Ditambahkan!', $category);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return new CategoryResource('error', 'Kategori tidak ditemukan!', null);
        }

        return new CategoryResource('success', 'Detail Data Kategori!', $category);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return new CategoryResource('error', 'Validation Error', $validator->errors());
        }

        $category = Category::find($id);

        if (!$category) {
            return new CategoryResource('error', 'Kategori tidak ditemukan!', null);
        }

        $category->update($request->all());

        return new CategoryResource('success', 'Data Kategori Berhasil Diubah!', $category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return new CategoryResource('error', 'Kategori tidak ditemukan!', null);
        }

        $category->delete();

        return new CategoryResource('success', 'Data Kategori Berhasil Dihapus!', null);
    }
}
