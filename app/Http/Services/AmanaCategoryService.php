<?php

namespace App\Http\Services;
use Illuminate\Support\Facades\Validator;
use App\Models\AmanaCategory;
class AmanaCategoryService{

    public function store($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $path = '';
            $folderPath = "storage/app/amanaCategory/";
                    $image_base64 = base64_decode($request->image);
                    $path = uniqid() . '.jpg';
                    $file = $folderPath . $path;
                    file_put_contents($file, $image_base64);

            $category = AmanaCategory::create([
                'title' => $request->title,
                'path' => $path,
                'type' => 0,
            ]);
            return response()->json(['success' => true], 200);
        }
    }
}