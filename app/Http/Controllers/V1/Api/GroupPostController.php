<?php

namespace App\Http\Controllers\V1\Api;

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
use App\Services\GroupPostService;
use App\Models\notification;
use App\Traits\upload;
class GroupPostController extends Controller
{
    use upload;
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
            'type' => 'required',
            'anonym' => 'required'
        ]);
        $is_approved = 0;
    
        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }
    
        if($validator->validated())
        {
            $path = '';
            $videoPath = '';
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

            
            if(strlen($request->video) != 0)
            {
                $videoPath = $this->VideoUpload($request->video,'videoPost');
            }

            $post = GroupPost::create([
                'user_id' => Auth::user()->id,
                'group_id' => ($request->group_id == 0) ? null : $request->group_id,
                'description' => (strlen($request->description) != 0) ? $request->description : '',
                'source' => ($request->source == null) ? '' : $request->source,
                'colorabble' => $request->colorabble,
                'likes' => 0,
                'type' => $request->type,
                'is_approved' => $is_approved,
                'anonym' => $request->anonym,
                'title_pitch' => (@$request->title_pitch) ? $request->title_pitch : '',
                'video' => (strlen($videoPath) != 0) ? $videoPath : '',
            ]);
    
            if(strlen($request->images) != 0)
            {
                $images = explode(';ibaa;',$request->images);
                foreach ($images as $image) {
                    $path = $this->ImageUpload($image,'postImages');
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = GroupPost::with('images','likesList','comments')->find($id);
            $user = User::where('id',$data->user_id)->select('id','fullName','subName','dob','picture','gender',
            'profession','phone','email','is_kaiztech_team')->first();
            $group = Group::where('id',$data->group_id)->first();
            $likeList = array();
            $dislikeList = array();
            $temp = $data->likesList;
            $data['is_kaiztech_team'] = $user->is_kaiztech_team;
            $data['user'] = $user;
            $data['user']['fullName'] = (strlen($data['user']['subName']) != 0) ? $data['user']['subName'] : $data['user']['fullName'];
            if($data->type == 2)
            {
                $data['video'] = env('DISPLAY_PATH') .'videoPost/'.$data->video;
            } 

            if($group)
            {
                $checkFollowingGroup = followGroup::where([['user_id','=',Auth::user()->id],['follow_id','=',$group->id]])->first();
                if($checkFollowingGroup)
                {
                    $data['is_following'] = 1;  
                }else{
                    $data['is_following'] = 0;
                }
                if($group->user_id == $data->user_id)
                {
                $data['is_admin'] = 1;
                $data['user']['picture'] =$group->cover;
                $data['user']['profession'] = 'Admin du group';
                $data['user']['fullName'] = $group->name;
                }else{
                    $data['is_admin'] = 0;
                }
                $data['group_name'] = $group->name;
                $data['group_pic'] = env('DISPLAY_PATH') .'groupImages/'.$group->cover;
            }else{
                $data['is_admin'] = 0;
                $data['group_name'] = '';
                $data['group_pic'] = '';
                $data['is_following'] = 0;
            }

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
        $validator = Validator::make($request->all(), [
            'description' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => 'hahah'], 200);
        }

        if($validator->validated())
        {
            $GroupPost = GroupPost::where('id',$id)->update([
                'description' => $request->description
            ]);
        if($GroupPost)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
        }
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
        if($id == 0)
        {
            $ids = array();
            $filters = GroupPost::whereDate('created_at', '>=', Carbon::now()->subDays(30)->setTime(0, 0, 0)->toDateTimeString())
            ->get();

            foreach ($filters as $filter) {
                if($filter->group_id == null)
                {
                    $ids[] = $filter->id;
                }else{
                    $grp = Group::where('id',$filter->group_id)->first();
                    if($grp->user_id == $filter->user_id)
                    {
                        $ids[] = $filter->id;
                    }
                }
            }

            $data = GroupPost::with('likesList')->whereIn('id',$ids)
            ->select('id','description','user_id','colorabble','type','anonym','group_id','title_pitch','created_at','video')->orderBy('id','DESC')->paginate(20);
             foreach ($data as $value) {
                $tempImages = array();
                 if($value['type'] == 2)
                 {
                    $value['video'] = env('DISPLAY_PATH') .'videoPost/'.$value->video;
                 }
            $row = GroupPost::withCount('comments')->find($value->id);
            $value['comment'] = $row->comments_count;
            $value['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $countLikes = 0;
            $countDislikes = 0;
            $temp = $value->likesList;
            foreach ($temp as $vl) {
                if($vl->type == 1)
                {
                  $countLikes++;
                }
                if($vl->type == -1)
                {
                  $countDislikes++;
                }
            }
            $value['dislikes'] = $countDislikes;
            $value['likes'] = $countLikes;

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
            $value['user'] = (strlen($user->subName) != 0) ? $user->subName : $user->fullName;
            $value['picture'] = env('DISPLAY_PATH') . 'profiles/' . $user->picture;
            $value['is_admin'] = 0;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
            if($value->group_id != null)
            {
                $group = Group::where('id',$value->group_id)->first();
                $value['user'] = $group->name;
                $value['picture'] = env('DISPLAY_PATH') . 'groupImages/' . $group->cover;
                $value['is_admin'] = 1;
                $value['is_kaiztech_team'] = $user->is_kaiztech_team; 
            }
            }
        return response()->json($data, 200);
        }
        $follow = 0;
        $is_admin = 0;
        $count_followers = 0;
        $data = GroupPost::with('likesList')->where([['group_id','=',$id],['is_approved','=',1]])->orderBy('id','DESC')->whereDate('created_at', '>=', Carbon::now()->subDays(30)->setTime(0, 0, 0)->toDateTimeString())
         ->select('id','description','user_id','colorabble','type','group_id','anonym','title_pitch','created_at','video')->paginate(20);

         $groupCheck = Group::where('id',$id)->first();
         $count_followers = followGroup::where('follow_id',$id)->count();
         if($groupCheck)
         {
            if($groupCheck->user_id  !== Auth::user()->id)
            {
               $followings = followGroup::where('user_id',Auth::user()->id)->get();
               foreach ($followings as $following) {
                      if($following->follow_id == $groupCheck->id)
                      {
                          $follow = 1;
                          break;
                      }
               }
            }else{
               $follow = -1; 
               $is_admin = 1;
            }
         }


        foreach ($data as $value) {
            $tempImages = array();
            if($value->type == 2)
                 {
                    $value['video'] = env('DISPLAY_PATH') .'videoPost/'.$value->video;
                 }
            $row = GroupPost::withCount('comments')->find($value->id);
            $value['comment'] = $row->comments_count;
            $value['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $countLikes = 0;
            $countDislikes = 0;
            $temp = $value->likesList;
            foreach ($temp as $vl) {
                if($vl->type == 1)
                {
                  $countLikes++;
                }
                if($vl->type == -1)
                {
                  $countDislikes++;
                }
            }
            $value['dislikes'] = $countDislikes;
            $value['likes'] = $countLikes;

            $userPost = GroupPost::with('images')->where('id',$value->id)->first();
            $group = Group::where('id',$value->group_id)->first();
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
            if($group->user_id == $value->user_id)
                {
                $value['user'] = $group->name;
            $value['picture'] = env('DISPLAY_PATH') . 'groupImages/' .$group->cover;
            $value['is_admin'] = 1;
                }else{
                $value['user'] = (strlen($user->subName) != 0) ? $user->subName : $user->fullName;
                $value['picture'] = env('DISPLAY_PATH') . 'profiles/' .$user->picture;
                $value['is_admin'] = 0;
                }
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
            $value['groupName'] = '';
            $value['pictureGroup'] = '';
        }
        $custom = collect(['following' => $follow,'is_admin' => $is_admin,'count_followers' => $count_followers]);

        $data = $custom->merge($data);

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
            $path = '';
            if($request->type == 2)
            {
                    $folderPath = env('MAIN_PATH') . "ImageComment/";
                    $image_base64 = base64_decode($request->comment);
                    $path = uniqid() . '.jpg';
                    $file = $folderPath . $path;
                    file_put_contents($file, $image_base64);
            }

            GroupPostComment::create([
                'group_post_id' => $request->group_post_id,
                'user_id' => Auth::user()->id,
                'comment' => ($request->type == 2) ? $path : $request->comment,
                'type' => $request->type,
            ]);

            $notification = notification::create([
                'user_id' => Auth::user()->id,
                'morphable_id' => $request->group_post_id,
                'type' => 2,
                'is_read' => 0
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
                
                $notification = notification::create([
                    'user_id' => Auth::user()->id,
                    'morphable_id' => $request->group_post_id,
                    'type' => ($request->type == 1) ? 0 : 1,
                    'is_read' => 0
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
        $notification = notification::create([
            'user_id' => Auth::user()->id,
            'morphable_id' => $group_post->id,
            'type' => 0,
            'is_read' => 0
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
            if($comment->type == 2)
            {
                $temp['comment'] = env('DISPLAY_PATH'). 'ImageComment/'.$comment->comment;
            }else{
                $temp['comment'] = $comment->comment;
            }
            $temp['type'] = $comment->type;
            $temp['created_at'] = $comment->created_at;
            $user = User::find($comment->user_id);
            $temp['pictureUser'] = $user->picture;
            $temp['fullName'] = (strlen($user->subName) != 0) ? $user->subName : $user->fullName;
            $temp['is_kaiztech_team'] = $user->is_kaiztech_team;
            $temp['user_id'] = $comment->user_id;
            $temp['group_post_id'] = $data->id;
            array_push($final,$temp);
        }    
         return response()->json($final, 200);
    }

    public function getPostsNotApprovedByCategory($id)
    {
        $checkGroup = Group::where('id',$id)->first();
        if($checkGroup->user_id == Auth::user()->id)
        {
            $data = GroupPost::where([['group_id','=',$id],['is_approved','=',0]])->orderBy('id','DESC')->whereDate('created_at', '>=', Carbon::now()->subDays(7)->setTime(0, 0, 0)->toDateTimeString())
            ->select('id','description','user_id','colorabble','type','title_pitch','video')->get();
   
           foreach ($data as $value) {
            if($value->type == 2)
            {
               $value['video'] = env('DISPLAY_PATH') .'videoPost/'.$value->video;
            }
               $userPost = GroupPost::with('images')->where('id',$value->id)->first();
               $group = Group::where('user_id',$value->user_id)->first();
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
                   $value['user'] = (strlen($user->subName) != 0) ? $user->subName : $user->fullName;
                   $value['picture'] = env('DISPLAY_PATH') . 'profiles/' .$user->picture;
                   $value['is_admin'] = 0;
               
               $value['is_kaiztech_team'] = $user->is_kaiztech_team;
           }
   
           return response()->json($data, 200);
        }

        return response()->json([], 200);
    }

    public function approveAllPosts($id)
    {
        $data = GroupPost::where([['group_id','=',$id],['is_approved',0]])->update([
            'is_approved' => 1
        ]);

        if($data)
        {
            return response()->json(['success' => true], 200);
        }

        return response()->json(['success' => false], 200);
    }

    public function decilnePost(Request $request)
    {
        $check = GroupPost::where('id',$request->group_post_id)->first();
        if($check->is_approved !== 0)
        {
            return response()->json(['success' => false], 200);
        }

        $group = Group::where('id',$check->group_id)->first();

        if($group->user_id == Auth::user()->id)
        {
            $data = GroupPost::where('id',$request->group_post_id)->update([
                'is_approved' => -1
            ]);
            if($data)
            {
                return response()->json(['success' => true], 200);
            }
            
            return response()->json(['success' => false], 200);
        }

        return response()->json(['success' => false], 200);
    }

    public function approvePost(Request $request)
    {
        $check = GroupPost::where('id',$request->group_post_id)->first();
        if($check->is_approved !== 0)
        {
            return response()->json(['success' => false], 200);
        }
        $group = Group::where('id',$check->group_id)->first();

        if($group->user_id == Auth::user()->id)
        {
            $data = GroupPost::where('id',$request->group_post_id)->update([
                'is_approved' => 1
            ]);
            if($data)
            {
                return response()->json(['success' => true], 200);
            }
            
            return response()->json(['success' => false], 200);
        }

        return response()->json(['success' => false], 200);
    }

    public function checkUserIsAdminOfGroup($group_id)
    {
        $group = Group::findOrFail($group_id);
        if($group->user_id == Auth::user()->id)
        {
            return response()->json(['success' => true], 200);
        }else{
            return response()->json(['success'=> false], 200);
        }
    }

    public function getTheLatestPost()
    {
        $post = GroupPost::select('id','description','user_id','colorabble','type','anonym','group_id','title_pitch','created_at','video')->latest()->first();
        $tempImages = array();
                 if($post['type'] == 2)
                 {
                    $post['video'] = env('DISPLAY_PATH') .'videoPost/'.$post->video;
                 }
            $row = GroupPost::withCount('comments')->latest()->first();
            $post['comment'] = $row->comments_count;
            $post['createdAt'] = Carbon::parse($post->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $likeList = array();
            $dislikeList = array();
            $temp = $post->likesList;
            foreach ($temp as $vl) {
                if($vl->type == -1)
           {
               array_push($dislikeList,$vl->user_id);
           }

           if($vl->type == 1)
           {
            array_push($likeList,$vl->user_id);
           }
            }
            $post['dislikes'] = count($dislikeList);
            $post['likes'] = count($likeList);

            $userPost = GroupPost::with('images')->latest()->first();
            $user = User::where('id',$post->user_id)->first();
            if(@$userPost->images[0]->path)
            {
                if(count($userPost->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userPost->images[$i]); 
                }
                $post['images'] = $tempImages;
            }else{
                $post['images'] = $userPost->images;
            }
            $post['countImages'] = count($userPost->images);
            }else{
                $post['images'] = [];
                $post['countImages'] = 0;
            }
            $post['user'] = $user->fullName;
            $post['picture'] = env('DISPLAY_PATH') . 'profiles/' . $user->picture;
            $post['is_admin'] = 0;
            $post['is_kaiztech_team'] = $user->is_kaiztech_team;
            if($post->group_id != null)
            {
                $group = Group::where('id',$post->group_id)->first();
                $post['user'] = $group->name;
                $post['picture'] = env('DISPLAY_PATH') . 'groupImages/' . $group->cover;
                $post['is_admin'] = 1;
                $post['is_kaiztech_team'] = $user->is_kaiztech_team; 
            }
            return response()->json($post, 200);
    }

    public function deleteCommentFromPost($id_comment = null,$group_post_id = null)
    {
        $deleted = GroupPostComment::where('id',$id_comment)->delete();
        if($deleted)
        {
            $data = $this->commentsByPost($group_post_id);
            return response()->json($data, 200);
        }
        return response()->json(['success' => false], 200);
    }

    public function updateComment(Request $request,$comment_id = null)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $comment = GroupPostComment::where('id',$comment_id)->update([
                'comment' => $request->comment
            ]);
            if($comment)
            {
                return response()->json(['success' => true], 200);
            }
            return response()->json(['success' => false], 200);
        }
    }


    
}
