<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Design;
use App\Http\Resources\DesignResource;
use Illuminate\Support\Facades\Validator;

class DesignController extends Controller
{
    public function index()
    {
        $designs = Design::all();
        return new DesignResource(true, 'List Data Designs', $designs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new DesignResource(false, 'Validation Error', $validator->errors());
        }

        $design = Design::create([
            'link' => $request->link,
        ]);

        return new DesignResource(true, 'Data Design Berhasil Ditambahkan!', $design);
    }

    public function show($id)
    {
        $design = Design::find($id);

        if (!$design) {
            return new DesignResource(false, 'Design not found!', null);
        }

        return new DesignResource(true, 'Detail Data Design!', $design);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'string',
        ]);

        if ($validator->fails()) {
            return new DesignResource(false, 'Validation Error', $validator->errors());
        }

        $design = Design::find($id);

        if (!$design) {
            return new DesignResource(false, 'Design not found!', null);
        }

        $design->update($request->all());

        return new DesignResource(true, 'Data Design Berhasil Diubah!', $design);
    }

    public function destroy($id)
    {
        $design = Design::find($id);

        if (!$design) {
            return new DesignResource(false, 'Design not found!', null);
        }

        $design->delete();

        return new DesignResource(true, 'Data Design Berhasil Dihapus!', null);
    }

    public function getAllDesign()
    {
        $designs = Design::all();
        return new DesignResource(true, 'List Data Designs', $designs);
    }

    public function getDesignBySlug($slug)
    {
        $design = Design::where('slug', $slug)->first();

        if (!$design) {
            return new DesignResource(false, 'Design not found!', null);
        }

        return new DesignResource(true, 'Detail Data Design!', $design);
    }
}
