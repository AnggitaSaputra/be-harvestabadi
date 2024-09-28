<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArtikelResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::with('author', 'category')->get();
        return new ArtikelResource('success', 'List Data Artikels', $artikels);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'author' => 'required|integer',
            'slug' => 'required|string',
            'content' => 'required|string',
            'category' => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return new ArtikelResource('error', 'Validation Error', $validator->errors());
        }

        $image = $request->file('image');
        $imagePath = Storage::putFileAs('public/images', $image, $image->hashName());

        $artikel = Artikel::create([
            'title' => $request->title,
            'image' => basename($imagePath),
            'author' => $request->author,
            'slug' => $request->slug,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        return new ArtikelResource('success', 'Data Artikel Berhasil Ditambahkan!', $artikel);
    }

    public function show($id)
    {
        $artikel = Artikel::with('author', 'category')->find($id);

        if (!$artikel) {
            return new ArtikelResource('error', 'Artikel not found!', null);
        }

        return new ArtikelResource('success', 'Detail Data Artikel!', $artikel);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'author' => 'integer',
            'slug' => 'string',
            'content' => 'string',
            'category' => 'integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return new ArtikelResource('error', 'Validation Error', $validator->errors());
        }

        $artikel = Artikel::with('author', 'category')->find($id);

        if (!$artikel) {
            return new ArtikelResource('error', 'Artikel not found!', null);
        }

        $artikel->update($request->all());

        return new ArtikelResource('success', 'Data Artikel Berhasil Diubah!', $artikel);
    }

    public function destroy($id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return new ArtikelResource('error', 'Artikel not found!', null);
        }

        $artikel->delete();

        return new ArtikelResource('success', 'Data Artikel Berhasil Dihapus!', null);
    }

    public function getAllArtikel()
    {
        $artikels = Artikel::with('author', 'category')->get();
        return new ArtikelResource('success', 'List Data Artikels', $artikels);
    }

    public function getArtikelBySlug($slug)
    {
        $artikel = Artikel::with('author', 'category')->where('slug', $slug)->first();

        if (!$artikel) {
            return new ArtikelResource('error', 'Artikel not found!', null);
        }

        return new ArtikelResource('success', 'Detail Data Artikel!', $artikel);
    }

    public function getArtikelByCategory($category)
    {
        $artikels = Artikel::where('category', $category)->get();
        return new ArtikelResource('success', 'List Data Artikels by Category', $artikels);
    }

    public function getArtikelByQuery($query)
    {
        $artikels = Artikel::where('title', 'LIKE', "%{$query}%")->get();
        return new ArtikelResource('success', 'List Data Artikels by Query', $artikels);
    }

    public function getArtikelByYear($year)
    {
        $artikels = Artikel::whereYear('created_at', $year)->get();
        return new ArtikelResource('success', 'List Data Artikels by Year', $artikels);
    }
}
