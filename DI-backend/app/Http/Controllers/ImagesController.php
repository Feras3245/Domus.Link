<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Immobilie;
use App\Models\User;
use App\Models\Image;
use Storage;

class ImagesController extends Controller
{
    public function retrieve(Request $request, $owner_type, $owner_id)
    {
        $id = $request->query('id');
        $size = $request->query('size');
        if (!in_array($size, ['large', 'medium', 'small'])) {
            return response()->json(['error' => 'Invalid size'], 400);
        }

        $path = "images/".$owner_type."/".$owner_id."/".$size."/".$id.".webp";
        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        return response()->file(Storage::disk('local')->path($path), ["Content-Type" => "image/webp"]);
    }


    public function delete(Request $request)
    {
        $fields = $request->validate([
            'image' => [
                'string',
                'ulid',
                'required_without_all:images',
                'prohibited_if:images,*',
            ],
            'images' => [
                'array',
                'required_without_all:image',
                'prohibited_if:image,*',
            ],
            'images.*' => [
                'string',
                'ulid',
            ]
        ]);

        if (array_key_exists('image',$fields)) {
            $image = Image::find($fields['image']);
            if (!$image) {
                return response()->json(['error' => 'Image not found or does not belong to the specified owner'], 404);
            }

            $image->delete();
            return response(["message"=>"image deleted"], 200);
        } else {
            $images = [];
            $count = 0;
            foreach($fields['images'] as $img) {
                $image = Image::find($img);
                if (!$image) {
                    return response()->json(['error' => 'Image not found or does not belong to the specified owner'], 404);
                }
                $images[$count] = $image;
                $count++;
            }
            foreach($images as $image) {
                $image->delete();
            }
            return response(["message"=>"images deleted"], 200);
        }
    }

    public function create(Request $request, $owner_type, $owner_id)
    {
        if (!in_array($owner_type, ['immobilien', 'users'])) {
            return response()->json(['error' => 'Invalid model type'], 400);
        }

        $modelClass = $owner_type === 'users' ? User::class : Immobilie::class;
        $owner = $modelClass::find($owner_id);
        if (!$owner) {
            return response()->json(['error' => 'Owner not found'], 404);
        }

        $files = $request->allFiles();
        foreach($files as $file) {
            $valid = str_contains($file->getMimeType(), 'image/');
            if (!$valid) {
                return response()->json([
                    'error' => 'Invalid file detected!'
                ], 400);
            }
        }

        $image_ids = [];
        foreach($files as $file) {
            $image = new Image();
            $image->file = $file;
            $image->owner()->associate($owner);
            $image->save();
            array_push($image_ids, $image->id);
        }

        return response($image_ids, 200);
    }
}
