<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\ChatSetting;
use App\Models\follower;
use App\Models\GroupPost;
use App\Models\User;
use App\Traits\upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ChatController extends Controller
{
    use upload;

    public function index(Request $request)
    {
        $messages = Chat::where('sender_id', Auth::id())->where('receiver_id', $request->receiver_id)->paginate(20);
        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $chat = Chat::create([
            'message' => $request->message,
            'type' => $request->type,
        ]);
        $chat->user()->attach($request->sender_user_id, ['received_user_id' => $request->received_user_id]);
        return true;
    }

    public function friends()
    {
        $first_ids_result = follower::whereUserId(Auth::id())->whereIsFriend(true)->pluck('id')->toArray();
        $second_ids_result = follower::whereFollowId(Auth::id())->whereIsFriend(true)->pluck('id')->toArray();
        $ids = array_merge($first_ids_result, $second_ids_result);
        $data = User::find($ids);
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);

    }

    public function seen(Request $request)
    {
        Chat::where('chat_id', $request->chat_id)->update([
            'seen' => true
        ]);
        return true;
    }

    public function destroy(Request $request)
    {
        Chat::find($request->chat_id)->delete();
        return true;
    }

    public function createChatGroup(Request $request)
    {
        $path = '';
        if ($request->image) {
            $path = $this->ImageUpload($request->video, 'chatGroups');
        }
        GroupPost::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'image' => $path,
        ]);
        return true;
    }

    public function deleteChatGroup(Request $request)
    {
        GroupPost::find($request->chat_group_id)->delete();
        return true;
    }

    public function createChatUsersGroup(Request $request)
    {
        $chat_group_id = $request->chat_group_id;
        $user_ids = $request->user_ids;
        $chat_group_id->users()->attach([$user_ids]);
        return true;
    }

    public function usersGroup(Request $request)
    {
        $users_group = ChatGroup::with('users,id,fullName,picture')->find($request->group_id);
        return response()->json([
            'success' => true,
            'items' => $users_group,
        ], 200);
    }

    public function deleteUserFromGroup(Request $request)
    {
        $user_id = $request->user_id;
        $group_chat_id = $request->group_chat_id;
        $group_chat_id->users()->sync($user_id);
        return true;
    }

    public function create_chat_settings(Request $request)
    {
        ChatSetting::create([
            'user_id' => $request->user_id
        ]);
        return true;
    }

    public function update_chat_settings(Request $request)
    {
        ChatSetting::whereUserId($request->user_id)->update([
            'block_notification' => $request->boolean('block_notification')
        ]);
        return true;
    }

    public function get_chat_settings(Request $request)
    {
        $chat_settings = ChatSetting::whereUserId($request->user_id)->first()->get('block_notification');
        return response()->json($chat_settings, true);
    }
}
