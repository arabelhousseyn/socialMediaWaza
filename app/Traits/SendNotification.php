<?php


namespace App\Traits;

use App\Models\Admin;
use App\Models\User;

trait SendNotification
{
    public function sendNotification()
    {
        $firebaseToken = Admin::pluck('device_token')->all();
        $SERVER_API_KEY = env('SERVER_KEY_TARGET_ADMIN');
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => 'Nouveaux utilisateur sur waza',
                "body" => 'Nouvel utilisateur en attente de confirmation sur waza',
                'image' => 'https://dashboard.waza.fun/waza-small.png',
                'sound' => true,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return curl_exec($ch);
    }

    public function sendNotificationForAddFriend($message,$user_id)
    {
        $tokens = array();
        $user = User::find($user_id);
        $tokens[] = $user->device_token;
        $SERVER_API_KEY = env('SERVER_KEY_TARGET_USERS');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'invitation',
                "body" => $message,
                'image' => 'https://dashboard.waza.fun/waza-small.png',
                'sound' => true,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return curl_exec($ch);
    }

    public function sendNotificationForNewCreatedGroup($message,$user_id)
    {
        $tokens = array();
        $user = User::find($user_id);
        $tokens[] = $user->device_token;

        $SERVER_API_KEY = env('SERVER_KEY_TARGET_USERS');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'Nouveaux groupe',
                "body" => $message,
                'image' => 'https://dashboard.waza.fun/waza-small.png',
                'sound' => true,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return curl_exec($ch);
    }

    public function friendAccepted($user_id,$message)
    {
        $tokens = array();
        $user = User::find($user_id);
        $tokens[] = $user->device_token;
        $SERVER_API_KEY = env('SERVER_KEY_TARGET_USERS');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'Votre réseaux s\'élargit',
                "body" => $message,
                'image' => 'https://dashboard.waza.fun/waza-small.png',
                'sound' => true,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return curl_exec($ch);
    }

    public function InteractWithPost($user_id,$message)
    {
        $tokens = array();
        $user = User::find($user_id);
        $tokens[] = $user->device_token;
        $SERVER_API_KEY = env('SERVER_KEY_TARGET_USERS');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'Nouvelle Interaction',
                "body" => $message,
                'image' => 'https://dashboard.waza.fun/waza-small.png',
                'sound' => true,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return curl_exec($ch);
    }

    public function commentPost($user_id,$message)
    {
        $tokens = array();
        $user = User::find($user_id);
        $tokens[] = $user->device_token;
        $SERVER_API_KEY = env('SERVER_KEY_TARGET_USERS');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'Nouveaux commentaire',
                "body" => $message,
                'image' => 'https://dashboard.waza.fun/waza-small.png',
                'sound' => true,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return curl_exec($ch);
    }
}
