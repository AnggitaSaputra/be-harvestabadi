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
        return new AboutResource('success', 'About Us data retrieved successfully.', $about);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'string',
        ]);

        if ($validator->fails()) {
            return new AboutResource('error', 'Validation Error', $validator->errors());
        }

        $about = About::first();


        if (!$about) {
            return new AboutResource('error', 'About Us not found!', null);
        }

        $about->update($request->all());

        return new AboutResource('success', 'Data About Us Berhasil Diubah!', $about);
    }
}
