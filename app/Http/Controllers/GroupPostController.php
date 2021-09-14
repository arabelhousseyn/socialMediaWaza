<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\GroupPost;
use App\Models\GroupPostImage;
use App\Models\User;
use App\Models\GroupPostComment;
use App\Models\GroupPostLike;
use Illuminate\Support\Facades\Validator;
class GroupPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $path = '';
            if(strlen($request->images) != 0)
            {
               $post = GroupPost::create([
                    'user_id' => Auth::user()->id,
                    'group_id' => ($request->group_id == 0) ? null : $request->group_id,
                    'description' => $request->description,
                    'source' => ($request->source == null) ? '' : $request->source,
                    'colorabble' => $request->colorabble,
                    'likes' => 0,
                    'type' => $request->type,
                ]);

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
            

            GroupPost::create([
                'user_id' => Auth::user()->id,
                'group_id' => ($request->group_id == 0) ? null : $request->group_id,
                'video' => '',
                'description' => $request->description,
                'source' => ($request->source == null) ? '' : $request->source,
                'colorabble' => $request->colorabble,
                'likes' => 0,
                'type' => $request->type,
            ]);
            return response()->json(['success' => true], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = GroupPost::with('user','images','likesList','comments')->find($id);
         if($data)
         {
            $likeList = array();
            $dislikeList = array();
            
            $temp = $data->likesList;
            $data['pictureUser'] = $data->user->picture;
            $data['is_kaiztech_team'] = $data->user->is_kaiztech_team;
            foreach ($temp as $value) {
                if($value->type == -1)
           {
               array_push($dislikeList,$value->user_id);
           }

           if($value->type == 1)
           {
            array_push($likeList,$value->user_id);
           }
            }
            $data['dislikeList'] = $dislikeList;
            $data['likeList'] = $likeList;

            return response()->json(['success' => true,'data' => $data], 200);
         }
         return response()->json(['success' => false], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $GroupPost = GroupPost::where('id',$id)->update($request);
        if($GroupPost)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $GroupPost = GroupPost::where('id',$id)->delete();
        if($GroupPost)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
    }

    public function getPostsByCategory($id)
    {
        $tempImages = array();
        if($id == 0)
        {
            $data = GroupPost::where('group_id',null)->select('id','description','user_id','colorabble','type')->orderBy('id','DESC')->paginate(20);
             foreach ($data as $value) {
            $userPost = GroupPost::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            if(@$userPost->images[0]->path)
            {
                if(count($userPost->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userPost->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userPost->images;
            }
            $value['countImages'] = count($userPost->images);
            }else{
                $value['images'] = [];
                $value['countImages'] = 0;
            }
            $value['user'] = $user->fullName;
            $value['pictureUser'] = $user->picture;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
            }
        return response()->json($data, 200);
        }

        $data = GroupPost::where('group_id','=',$id)->orderBy('id','DESC')
         ->select('id','description','user_id','colorabble','type')->paginate(20);

        foreach ($data as $value) {
            $userPost = GroupPost::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            if(@$userPost->images[0]->path)
            {
                if(count($userPost->images) > 5)
                {
                    for ($i=0; $i <5 ; $i++) {
                        array_push($tempImages,$userPost->images[$i]); 
                    }
                    $value['images'] = $tempImages;
                }else{
                    $value['images'] = $userPost->images;
                }
                $value['countImages'] = count($userPost->images);
            }else{
                $value['images'] = [];
                $value['countImages'] = 0;
            }
            $value['user'] = $user->fullName;
            $value['pictureUser'] = $user->picture;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }

        return response()->json($data, 200);
    }

    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_post_id' => 'required',
            'comment' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            GroupPostComment::create([
                'group_post_id' => $request->group_post_id,
                'user_id' => Auth::user()->id,
                'comment' => $request->comment,
                'type' => $request->type,
            ]);
            $data = $this->commentsByPost($request->group_post_id);
            return response()->json($data->original, 200);
        }
    }

    public function hanldeAction(Request $request)
    {
        $check = GroupPostLike::where([['user_id','=',Auth::user()->id],['group_post_id','=',$request->group_post_id]])->first();

        if($check)
        {
            if($request->type == $check->type)
            {
                GroupPostLike::where([['user_id','=',Auth::user()->id],['group_post_id','=',$request->group_post_id]])->delete();  
            }else{
                $check->update([
                    'type' => $request->type
                ]);    
            }
        $data = $this->likeListByPost($request->group_post_id);

        return response()->json($data->original, 200);
        }

        $like = GroupPostLike::create([
            'user_id' => Auth::user()->id,
            'group_post_id' => $request->group_post_id,
            'type' => $request->type
        ]);

        $group_post = GroupPost::findOrFail($request->group_post_id);
        $likes = $group_post->likes + 1;
        $group_post->update([
            'likes' => $likes
        ]); 
        $data = $this->likeListByPost($request->group_post_id);

        return response()->json($data->original, 200);
    }

    public function likeListByPost($id)
    {
        $data = GroupPost::with('user','likesList')->find($id);
        $final = array();
        $temp = $data->likesList;
        $likeList = array();
        $dislikeList = array();
        foreach ($temp as $value) {
           if($value->type == -1)
           {
               array_push($dislikeList,$value->user_id);
           }

           if($value->type == 1)
           {
            array_push($likeList,$value->user_id);
           }
        }

        $final['dislikeList'] = $dislikeList;
        $final['likeList'] = $likeList;

         return response()->json($final, 200);
    }
    public function commentsByPost($id)
    {
        $data = GroupPost::with('comments')->find($id);
        $temp = array();
        $final = array();
        $comments = $data->comments;
        foreach ($comments as $comment) {
            $temp['id'] = $comment->id;
            $temp['comment'] = $comment->comment;
            $temp['type'] = $comment->type;
            $temp['created_at'] = $comment->created_at;
            $user = User::find($comment->user_id);
            $temp['pictureUser'] = $user->picture;
            $temp['fullName'] = $user->fullName;
            $temp['is_kaiztech_team'] = $user->is_kaiztech_team;
            array_push($final,$temp);
        }    
         return response()->json($final, 200);
    }
}
