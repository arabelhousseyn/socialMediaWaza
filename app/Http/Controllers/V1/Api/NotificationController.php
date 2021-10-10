<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\notification;
use App\Models\GroupPostLike;
use App\Models\GroupPostComment;
use App\Models\GroupPost;
use App\Models\Group;
use App\Models\User;
use App\Models\follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Carbon\Carbon;
use App\Traits\SendNotification;
class NotificationController extends Controller
{
    use SendNotification;
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
        $final = array();
        $data = notification::where('is_read',0)->whereDate('created_at', '>=', Carbon::now()->subDays(7)->setTime(0, 0, 0)->toDateTimeString())->get();
        foreach ($data as $value) {
            $temp = array();
            // if($value->type == 0 || $value->type == 1)
            // {
            //     $likes = GroupPostLike::where('group_post_id',$value->morphable_id)->get();
            //     foreach ($likes as $like) {
            //       $post = GroupPost::find($like);
            //       if($post->user_id == $user_id)
            //       {

            //       } 
            //     }
            // }
            //     if($value->type == 2)
            // {
            //     $user = User::where('id',$value->user_id)->first();
            //     $comments = GroupPostComment::where('group_post_id',$value->morphable_id)->get();
            //     foreach ($comments as $comment) {
            //       $post = GroupPost::find($comment->group_post_id);
            //       if($post->user_id == Auth::user()->id)
            //       {
            //           $temp['id'] = $value->id;
            //           $temp['message'] = $user->fullName . ' commenter votre publication';
            //           $temp['post_id'] = $post->id;
            //           $temp['type'] = 0;
            //           $temp['picture'] = env('DISPLAY_PATH') .'profiles/'. $user->picture;
            //           $final[] = $temp;
            //       } 
            //     }
            // }
            if($value->type == 3)
            {
                $user = User::where('id',$value->user_id)->first();
                $group = Group::where('id',$value->morphable_id)->first();
                if($group)
                {
                    if($group->type == 1 && Auth::user()->id != $group->user_id)
                {
                        $temp['id'] = $value->id;
                        $temp['message'] = $group->name . ' vient d\'être créé ! decouvrir ce contenu';
                        $temp['group_id'] = $group->id;
                        $temp['type'] = 1;
                        $temp['picture'] = env('DISPLAY_PATH') .'groupImages/'. $group->cover;
                        $final[] = $temp;
                }
                }
            }
        }

        return response()->json($final, 200);
    }

    public function getNotificationById($id)
    {
        $final = array();
        $data = notification::where('id',$id)->first();
        if($data)
        {
            if($data->type == 3)
            {
                $user = User::where('id',$data->user_id)->first();
                $group = Group::where('id',$data->morphable_id)->first();
                if($group)
                {
                    if($group->type == 1 && Auth::user()->id != $group->user_id)
                {
                        $message = $group->name . ' vient d\'être créé ! decouvrir ce contenu';
                        $final['id'] = $data->id;
                        $final['message'] = $message;
                        $final['group_id'] = $group->id;
                        $final['type'] = 1;
                        $final['picture'] = env('DISPLAY_PATH') .'groupImages/'. $group->cover;
                        $this->sendNotificationForNewCreatedGroup($message,$data->user_id);
                }
                return response()->json($final, 200);
                }
            }

            if($data->type == 4)
            {
                $user = User::where('id',$data->user_id)->first();
                $receive = User::where('id',$data->morphable_id)->first();
                $message = $user->fullName . ' souhaite vous ajouter a son réseaux';
                    $final['id'] = $data->id;
                    $final['message'] = $message;
                    $final['type'] = 4;
                    $final['picture'] = env('DISPLAY_PATH') .'profiles/'. $user->picture;
                    $this->sendNotificationForAddFriend($message,$data->morphable_id);
            return response()->json($final, 200);
            }

            return response()->json(['success' => false], 200);
        }
        return response()->json(['success' => false], 200);
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
        $data = notification::where([['is_read','=',0],['type','=',4]])->get();
        foreach ($data as $value) {
            $temp = array();
            $user = User::where('id',$value->user_id)->first();
            $recevie = User::where('id',$value->morphable_id)->first();
                if($recevie)
                {
                    if(Auth::user()->id == $recevie->id)
            {
                    $temp['id'] = $value->id;
                    $temp['user_id'] = $user->id;
                    $temp['message'] = $user->fullName . ' souhaite vous ajouter a son réseaux';
                    $temp['type'] = 4;
                    $temp['picture'] = env('DISPLAY_PATH') .'profiles/'. $user->picture;
                    $final[] = $temp;
            }
                }
        }
        return response()->json($final, 200);
    }

    public function InteractWithFriend($id,$statu)
    {
        switch ($statu) {
            case 0:
                $notification = notification::where('id',$id)->first();
                if($notification)
                {
                    $check = follower::where([['user_id','=',$notification->user_id],['follow_id','=',$notification->morphable_id]])->first();
                if($check && $notification->type == 4 && @$check->is_friend == 0)
                {
                    follower::where([['user_id','=',$notification->user_id],['follow_id','=',$notification->morphable_id]])->update([
                        'is_friend' => 1
                    ]);
                    notification::where('id',$id)->update([
                        'is_read' => 1
                    ]);
                    return response()->json(['success' => true], 200);
                }
                return response()->json(['success' => false], 200);
                }
                return response()->json(['success' => false], 200);
                break;
                case 1:
                    $notification = notification::where('id',$id)->first();
                     if($notification)
                     {
                        $check = follower::where([['user_id','=',$notification->user_id],['follow_id','=',$notification->morphable_id]])->first();
                        if($check && $notification->type == 4 && @$check->is_friend == 0)
                        {
                            follower::where([['user_id','=',$notification->user_id],['follow_id','=',$notification->morphable_id]])->update([
                                'is_friend' => -1
                            ]);
                            notification::where('id',$id)->update([
                                'is_read' => 1
                            ]);
                            return response()->json(['success' => true], 200);
                        }
                        return response()->json(['success' => false], 200);
                     }
                     return response()->json(['success' => false], 200);
                    break;
            
            default:
            return response()->json(['success' => false], 200);
                break;
        }
    }


    public function getNotificationsNotRead($id,$type)
    {
        switch ($type) {
            case 3:
                $data = notification::where([['is_read','=',0],['user_id','=',$id],['type','=',3]])
        ->orderBy('id','DESC')->count();
        return response()->json(['countNotification' => $data], 200);
                break;

                case 4:
                    $data = notification::where([['is_read','=',0],['morphable_id','=',$id],['type','=',4]])
        ->orderBy('id','DESC')->count();
        return response()->json(['countNotification' => $data], 200);
                    break;
            
            default:
                return response()->json(['success' => false], 200);
                break;
        }
    }


    public function friendsAccepted()
    {
        $final = array();
        $notifications = notification::where([['user_id','=',Auth::user()->id],['type','=',4],['is_read','=',1]])->get();
        foreach ($notifications as $notification) {
            $temp = array();
            $follower = follower::where([['user_id','=',Auth::user()->id],['follow_id','=',$notification->morphable_id]])->first();
            if($follower->is_friend == 1)
            {
                $user = User::find($notification->morphable_id);
                $temp['message'] = $user->fullName . ' accept votre invitation';
                $temp['type'] = 4;
                $temp['picture'] = env('DISPLAY_PATH') .'profiles/'. $user->picture;
                $final[] = $temp;
            }
        }
        return response()->json($final, 200);
    }


}
