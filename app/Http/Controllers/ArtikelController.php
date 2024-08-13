<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArtikelResource; 
use Illuminate\Support\Facades\Validator;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::all();
        return new ArtikelResource(true, 'List Data Artikels', $artikels);
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
            return new ArtikelResource(false, 'Validation Error', $validator->errors());
        }

        $artikel = Artikel::create($request->all());

        return new ArtikelResource(true, 'Data Artikel Berhasil Ditambahkan!', $artikel);
    }

    public function show($id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return new ArtikelResource(false, 'Artikel not found!', null);
        }

        return new ArtikelResource(true, 'Detail Data Artikel!', $artikel);
    }

    public function update(Request $request, $id)
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
            return new ArtikelResource(false, 'Validation Error', $validator->errors());
        }

        $artikel = Artikel::find($id);

        if (!$artikel) {
            return new ArtikelResource(false, 'Artikel not found!', null);
        }

        $artikel->update($request->all());

        return new ArtikelResource(true, 'Data Artikel Berhasil Diubah!', $artikel);
    }

    public function destroy($id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return new ArtikelResource(false, 'Artikel not found!', null);
        }

        $artikel->delete();

        return new ArtikelResource(true, 'Data Artikel Berhasil Dihapus!', null);
    }

    public function getAllArtikel()
    {
        $artikels = Artikel::all();
        return new ArtikelResource(true, 'List Data Artikels', $artikels);
    }

    public function getArtikelBySlug($slug)
    {
        $artikel = Artikel::where('slug', $slug)->first();
        
        if (!$artikel) {
            return new ArtikelResource(false, 'Artikel not found!', null);
        }

        return new ArtikelResource(true, 'Detail Data Artikel!', $artikel);
    }

    public function getArtikelByCategory($category)
    {
        $artikels = Artikel::where('category', $category)->get();
        return new ArtikelResource(true, 'List Data Artikels by Category', $artikels);
    }

    public function getArtikelByQuery($query)
    {
        $artikels = Artikel::where('title', 'LIKE', "%{$query}%")->get();
        return new ArtikelResource(true, 'List Data Artikels by Query', $artikels);
    }

    public function getArtikelByYear($year)
    {
        $artikels = Artikel::whereYear('created_at', $year)->get();
        return new ArtikelResource(true, 'List Data Artikels by Year', $artikels);
    }

}
