<?php

namespace App\Services;

use Illuminate\Http\Request;
use Auth;
use App\Models\GroupPost;
use App\Models\Group;
use App\Models\GroupPostImage;
use App\Models\User;
use App\Models\GroupPostComment;
use App\Models\GroupPostLike;
use App\Models\followGroup;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GroupPostService{
   public function store($request)
   {
    $validator = Validator::make($request->all(), [
        'description' => 'required',
        'type' => 'required',
    ]);
    $is_approved = 0;

    if($validator->fails())
    {
        return response()->json(['success' => false], 200);
    }

    if($validator->validated())
    {
        $path = '';
        $group = Group::where('id',$request->group_id)->first();
        if($request->group_id == 0)
        {
           $is_approved = 1; 
        }else{
            if($group->user_id == Auth::user()->id)
            {
                $is_approved = 1; 
            }else{
                $is_approved = 0; 
            }
        }
        $post = GroupPost::create([
            'user_id' => Auth::user()->id,
            'group_id' => ($request->group_id == 0) ? null : $request->group_id,
            'description' => $request->description,
            'source' => ($request->source == null) ? '' : $request->source,
            'colorabble' => $request->colorabble,
            'likes' => 0,
            'type' => $request->type,
            'is_approved' => $is_approved,
            'anonym' => $request->anonym,
        ]);

        if(strlen($request->images) != 0)
        {
            $images = explode(';ibaa;',$request->images);
            foreach ($images as $image) {
                $folderPath = env('MAIN_PATH') . "postImages/";
                $image_base64 = base64_decode($image);
                $path = uniqid() . '.jpg';
                $file = $folderPath . $path;
                file_put_contents($file, $image_base64);

                GroupPostImage::create([
                    'path' => $path,
                    'group_post_id' => $post->id
                ]);
            }

            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => true], 200);
    }
   }
}