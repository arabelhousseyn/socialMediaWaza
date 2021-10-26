<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\{
    notification,
    GroupPostLike,
    GroupPostComment,
    GroupPost,
    Group,
    User,
    follower
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Carbon\Carbon;
use App\Traits\{
    SendNotification,
    middlewares
};
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\pushNotification;
use Queue;
class NotificationController extends Controller
{
    use SendNotification,middlewares;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'morphable_id' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $notification = notification::create([
                'user_id' => Auth::user()->id,
                'morphable_id' => $request->morphable_id,
                'type' => $request->type,
            ]);
            if($notification)
            {
                return response()->json(['success' => true], 200);
            }

            return response()->json(['success' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(notification $notification)
    {
        //
    }

    public function getNotifications()
    {
        $ids = array();
        $final = array();
        $breakInteraction = array();
        $breakComment = array();
        $data = notification::where('is_read',0)->orderBy('id','DESC')->whereDate('updated_at', '>=', Carbon::now()->subDays(1)->setTime(0, 0, 0)->toDateTimeString())->get();
        foreach ($data as $value) {
            $temp = array();
            if($value->type == 0 || $value->type == 1)
            {
                // check if morphable_id is repeated
                if(!in_array($value->morphable_id,$breakInteraction))
                {
                    $post = GroupPost::with('images','likesList')->find($value->morphable_id);
                    if(@$post->likesList)
                    {
                        if(count($post->likesList) <= 5)
                {
                    foreach ($post->likesList as $like) {
                        if($post->user_id == Auth::user()->id && $like->user_id != Auth::user()->id)
                        {
                              $user = User::where('id',$like->user_id)->first();
                              $temp['id'] = $value->id;
                              $temp['message'] ='a interagi avec votre publication';
                              $temp['sub_message'] = $user->fullName;
                              $temp['link_id'] = $value->morphable_id;
                              $temp['user_id'] = $like->user_id;
                              $temp['type'] = 1;
                              $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                              if(count($post->images) > 0)
                              {
                                $temp['link_cover'] = $post->images[0]->path;
                              }else{
                                $temp['link_cover'] = '';  
                              }
                              $final[] = $temp;
                        } 
                      }
                }else{

                    if($post->user_id == Auth::user()->id)
                        {
                            $likes = count($post->likesList);
                            $last_user = User::find($post->likesList[$likes - 1]->user_id);
                            $temp['id'] = $value->id;
                            $temp['message'] = 'ont interagi sur votre publication';
                            if($last_user->id == Auth::user()->id)
                            {
                                $last_user = User::find($post->likesList[$likes - 2]->user_id);
                                $temp['sub_message'] = strval($last_user->fullName . ' et '. $likes - 1 . ' autres personnes');
                            }else{
                                $temp['sub_message'] = strval($last_user->fullName . ' et '. $likes - 1 . ' autres personnes');
                            }
                            $temp['link_id'] = $value->morphable_id;
                            $temp['type'] = 1;
                            if(count($post->images) > 0)
                              {
                                $temp['link_cover'] = $post->images[0]->path;
                              }else{
                                $temp['link_cover'] = '';  
                              }
                            $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                            $final[] = $temp;  
                        } 
                }
                    }
                $breakInteraction[] = $value->morphable_id;
                }
            }
                if($value->type == 2)
            {
                // check if morphable_id is repeated
                if(!in_array($value->morphable_id,$breakComment))
                {
                    $post = GroupPost::with('images','comments')->find($value->morphable_id);
                    if(@$post->comments)
                    {
                        if(count($post->comments) <= 5)
                {
                    foreach ($post->comments as $comment) {
                        if($post->user_id == Auth::user()->id && $comment->user_id != Auth::user()->id)
                        {
                                $user = User::where('id',$comment->user_id)->first();
                            $temp['id'] = $value->id;
                            $temp['message'] ='a commenté sur votre publication';
                            $temp['sub_message'] =$user->fullName;
                            $temp['user_id'] = $comment->user_id;
                            $temp['link_id'] = $value->morphable_id;
                            $temp['type'] = 2;
                            $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                            if(count($post->images) > 0)
                              {
                                $temp['link_cover'] = $post->images[0]->path;
                              }else{
                                $temp['link_cover'] = '';  
                              }
                            $final[] = $temp;
                            
                        } 
                      }
                }else{
                    if($post->user_id == Auth::user()->id)
                        {
                    $comments = count($post->comments);
                    $last_user = User::find($post->comments[$comments - 1]->user_id);
                    $temp['id'] = $value->id;
                    $temp['message'] = 'ont commenté sur votre publication';
                    if($last_user->id == Auth::user()->id)
                            {
                                $last_user = User::find($post->comments[$comments - 2]->user_id);
                                $temp['sub_message'] = strval($last_user->fullName . ' et '. $comments - 1 . ' autres personnes');
                            }else{
                                $temp['sub_message'] = strval($last_user->fullName . ' et '. $comments - 1 . ' autres personnes');
                            }
                    $temp['link_id'] = $value->morphable_id;
                    if(count($post->images) > 0)
                              {
                                $temp['link_cover'] = $post->images[0]->path;
                              }else{
                                $temp['link_cover'] = '';  
                              }
                    $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                    $temp['type'] = 2;
                    $final[] = $temp; 
                        }  
                }
                $breakComment[] = $value->morphable_id;
                    }
                }
            }
            if($value->type == 3)
            {
                $user = User::where('id',$value->user_id)->first();
                $group = Group::where('id',$value->morphable_id)->first();
                if($group)
                {
                    if(Auth::user()->id != $group->user_id)
                {
           if($group->gender == null)
           {
               if(strval($group->gender) != 0)
               {
                $temp['id'] = $value->id;
                $temp['message'] ='vient d\'être créé ! découvrez ce contenu';
                $temp['sub_message'] = $group->name;
                $temp['link_id'] = $group->id;
                $temp['type'] = 3;
                $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                $temp['link_cover'] = $group->cover;
                $final[] = $temp;
               }
           }else{
               // filter users
                   $age = Carbon::parse(Auth::user()->dob)->age;
                   if($age >= $group->minAge && $age <= $group->maxAge)
                   {
                    if($group->gender == 2)
                    {
                        $temp['id'] = $value->id;
                        $temp['message'] ='vient d\'être créé ! découvrez ce contenu';
                        $temp['sub_message'] = $group->name;
                        $temp['link_id'] = $group->id;
                        $temp['type'] = 3;
                        $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                        $temp['link_cover'] = $group->cover;
                        $final[] = $temp;
                    }else{
                       if($group->gender == $user->gender)
                       {
                        $temp['id'] = $value->id;
                        $temp['message'] = $group->name . ' vient d\'être créé ! découvrez ce contenu';
                        $temp['sub_message'] = $group->name;
                        $temp['link_id'] = $group->id;
                        $temp['type'] = 3;
                        $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                        $temp['link_cover'] = $group->cover;
                        $final[] = $temp;
                       } 
                    }
                   }
           }
                }
                }
            }

            if($value->type == 4)
            {
            $follower = follower::where([['user_id','=',Auth::user()->id],['follow_id','=',$value->morphable_id]])->first();
            if($follower)
            {
                if($follower->is_friend == 1)
            {
                if(Auth::user()->id == $value->user_id)
                {
                    $user = User::find($value->morphable_id);
                $temp['id'] = $value->id;
                $temp['message'] ='a accepté votre invitation';
                $temp['sub_message'] = $user->fullName;
                $temp['type'] = 4;
                $temp['link_id'] = $user->id;
                $temp['createdAt'] = Carbon::parse($value->updated_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
                $temp['link_cover'] = '';
                $final[] = $temp;
                }
            }
            }
            }
            
        }

        return response()->json($final, 200);
    }

    public function getNotificationById($id,$type)
    {
        $final = array();
        $data = notification::where('id',$id)->first();
        if($data)
        {
            if($type == 0)
            {
                if($data->type == 0 || $data->type == 1)
            {
                $user = User::where('id',$data->user_id)->first();
                    $post = GroupPost::find($data->morphable_id);
                        $message = $user->fullName . ' interagi sur votre publication';
                        $final['id'] = $data->id;
                        $final['message'] = $message;
                        $final['type'] = 1;
                        $final['user_id'] = $data->morphable_id;
                        $final['picture'] = $user->picture;
                        $this->push('Waza',$message,$post->user_id);
                        //$this->InteractWithPost($post->user_id,$message);
                return response()->json(['data' => $final,'user_id' => $post->user_id], 200);
            }
            }

            if($type == 2)
            {
                if($data->type == 2)
            {
                $user = User::find($data->user_id);
                $receiver = User::find($data->morphable_id);
                    $post = GroupPost::find($data->morphable_id);
                        $message = $user->fullName . ' commentez sur votre publication';
                        $final['id'] = $data->id;
                        $final['message'] = $message;
                        $final['type'] = 1;
                        $final['post_id'] = $data->morphable_id;
                        $final['picture'] = $user->picture;
                        $this->push('Waza',$message,$post->user_id);
                        return response()->json(['data' => $final,'user_id' => $post->user_id,'token' => $receiver->token], 200);
            }
            }

            if($type == 3)
            {
                if($data->type == 3)
            {
                $user = User::find($data->user_id);
                $group = Group::find($data->morphable_id);
                if($group)
                {
                    if(Auth::user()->id != $group->user_id)
                {
 
                        $message = $group->name . ' vient d\'être créé ! decouvrir ce contenu';
                        $final['id'] = $data->id;
                        $final['message'] = $message;
                        $final['group_id'] = $group->id;
                        $final['type'] = 1;
                        $final['picture'] = $group->cover;
                }
                return response()->json($final, 200);
                }
            }
            }

            if($type == 4)
            {
                if($data->type == 4)
            {
                $user = User::find($data->user_id);
                $receive = User::find($data->morphable_id);
                $message = $user->fullName . ' souhaite vous ajouter a son réseaux';
                    $final['id'] = $data->id;
                    $final['fullName'] = $user->fullName;
                    $final['type'] = 4;
                    $final['picture'] = $user->picture;
                    $this->push('Waza',$message,$data->morphable_id);
            return response()->json(['data' => $final,'user_id' => $receive->id], 200);
            }
            }

            if($type == 5)
            {
                if($data->type == 4)
            {
                $receive = User::find($data->user_id);
                $user = User::find($data->morphable_id);
                $message = $user->fullName . ' Accepte votre invitation';
                    $final['id'] = $data->id;
                    $final['message'] = $message;
                    $final['type'] = 4;
                    $final['picture'] = $user->picture;
                    return response()->json(['data' => $final,'user_id' => $receive->id,'token' => $receive->token], 200);
            }
            if($data->type == 5)
            {
                $user = User::find($data->user_id);
                $comment = GroupPostComment::with('user')->find($data->morphable_id);
                $message = $user->fullName . ' répondre à votre commentaire';
                    $final['id'] = $data->id;
                    $final['message'] = $message;
                    $final['type'] = 5;
                    $final['picture'] = $user->picture;
                    $this->push('Waza',$message,$comment->user->id);
                    return response()->json(['data' => $final,'user_id' =>$comment->user->id,'token' =>$comment->user->token], 200);
            }
            }

            return response()->json(['success' => false], 200);
        }
        return response()->json(['success' => false], 200);
    }

    public function sendPushNotification($notification_id = null)
    {
        $notification = notification::find($notification_id);
           $group = Group::find($notification->morphable_id);
           $message = $group->name . ' vient d\'être créé ! decouvrir ce contenu';
           if($group->gender == null)
           {
            if(strval($group->gender) != 0)
                {
                    $users = User::all();
                foreach ($users as $user) {
                    if(strlen($user->device_token) != 0)
                    {
                        $this->push('Waza',$message,$user->id);
                    }
                        // $push = new pushNotification($message,$user->id);
                        // dispatch($push);
                  }
                return response()->json(['success' => true], 200); 
                }
           }else{
               $users = User::all();
               foreach ($users as $user) {
                   $age = Carbon::parse($user->dob)->age;
                   if($age >= $group->minAge && $age <= $group->maxAge)
                   {
                    if($group->gender == 2)
                    {
                        $this->push('Waza',$message,$user->id);
                        // $push = new pushNotification($message,$user->id);
                        // dispatch($push)->delay(now()->addMinutes(2));
                    }else{
                       if($group->gender == $user->gender)
                       {
                        $this->push('Waza',$message,$user->id);
                        // $push = new pushNotification($message,$user->id);
                        // dispatch($push)->delay(now()->addMinutes(2));
                       } 
                    }
                   }
               }
               return response()->json(['success' => true], 200);
           }

    }

    public function getPureNotifcation($id)
    {
        $data = notification::where('id',$id)->first();
        return response()->json($data, 200);
    }

    public function updateRead($id)
    {
        $notification = notification::where('id',$id)->update([
            'is_read' => 1
        ]);

        if($notification)
        {
            return response()->json(['success' => true], 200);   
        }
        return response()->json(['success' => false], 200);
    }

    public function getAddFriends()
    {
        $final = array();
        $data = notification::where([['is_read','=',0],['type','=',4]])->orderBy('id','DESC')->get();
        foreach ($data as $value) {
            $temp = array();
            $user = User::where('id',$value->user_id)->first();
            $recevie = User::where('id',$value->morphable_id)->first();
            $follower = follower::where([['user_id','=',$value->user_id],['follow_id','=',$value->morphable_id]])->first();
            if($follower)
            {
                if($follower->is_friend != 1 && $follower->is_friend != -1)
            {
                if($recevie)
                {
                    if(Auth::user()->id == $recevie->id)
            {
                    $temp['id'] = $value->id;
                    $temp['user_id'] = $user->id;
                    $temp['fullName'] = $user->fullName;
                    $temp['picture'] = $user->picture;
                    $temp['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();;
                    $final[] = $temp;
            }
                }
            }
            }
        }
        return response()->json($final, 200);
    }

    public function InteractWithFriend($user_id,$status)
    {
        switch ($status) {
            case 1:
                $check = follower::where([['user_id','=',$user_id],['follow_id','=',Auth::user()->id]])->first();
                if($check && @$check->is_friend == 0)
                {
                    $notification = notification::where([['user_id','=',$user_id],['morphable_id','=',Auth::user()->id]])->first();
                    follower::where([['user_id','=',$user_id],['follow_id','=',Auth::user()->id]])->update([
                        'is_friend' => 1
                    ]);

                    return response()->json(['success' => true,'notification_id' => $notification->id], 200);
                }
                return response()->json(['success' => false], 200);

                break;
                case 0:
                    $check = follower::where([['user_id','=',$user_id],['follow_id','=',Auth::user()->id]])->first();
                if($check && @$check->is_friend == 0)
                {
                    follower::where([['user_id','=',$user_id],['follow_id','=',Auth::user()->id]])->update([
                        'is_friend' => -1
                    ]);

                    return response()->json(['success' => true], 200);
                }
                return response()->json(['success' => false], 200);
                    break;
            
            default:
            return response()->json(['success' => false], 200);
                break;
        }
    }


    public function getNotificationsNotRead()
    {
    $data = $this->getNotifications();
    return response()->json(['countNotification' => count($data->original)], 200);

    }


    public function friendsAccepted($notification_id = null)
    {
        $final = array();
            $notification = notification::find($notification_id);
            $temp = array();
            $follower = follower::where([['user_id','=',$notification->user_id],['follow_id','=',$notification->morphable_id]])->first();
            if($follower->is_friend == 1)
            {
                $user = User::find($notification->morphable_id);
                $message = $user->fullName . ' Fait désormais partie de votre réseaux';
                $temp['id'] = $notification->id;
                $temp['message'] = $message;
                $temp['type'] = 4;
                $temp['picture'] = $user->picture;
                $final[] = $temp;
                $this->push('Waza',$message,$notification->user_id);
            }
        return response()->json(['data' => $final,'user_id' => $notification->user_id], 200);
    }

}
