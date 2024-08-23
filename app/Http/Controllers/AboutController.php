<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AboutResource;

class AboutController extends Controller
{
    public function edit()
    {
        $about = About::first();
        return new AboutResource(true, 'About Us data retrieved successfully.', $about);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deskripsi' => 'string',
        ]);

        if ($validator->fails()) {
            return new AboutResource(false, 'Validation Error', $validator->errors());
        }

        $about = About::first();
        $about->content = $request->input('deskripsi');

        if (!$about) {
            return new AboutResource(false, 'About Us not found!', null);
        }

        $about->save();

        return new AboutResource(true, 'Data About Us Berhasil Diubah!', $about);
    }
}
