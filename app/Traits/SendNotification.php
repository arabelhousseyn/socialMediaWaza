<?php


namespace App\Traits;

use App\Models\Admin;
use App\Models\User;

trait SendNotification
{
    public function sendNotification()
    {
        $firebaseToken = Admin::pluck('device_token')->all();
        $SERVER_API_KEY = 'AAAAmWFyPSY:APA91bEPzsGOHpNkbg0A4MU2Mi3dmmgmnhimtubpZcCEagspY4S1OSU7Sjb2LdhJvM5cXAW9fPsang7zI7U0kHLRpCEOsQQRvGVUxJfyIpQ7YBjb_lSeTYkgJ-gGLcwrQNYwJ0qJbt_4';
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => 'Nouvelle utilisateur sur waza',
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
        $users = User::all();
        foreach ($users as $value) {
            if($value->id == $user_id)
            {
                $tokens[] = $value->device_token;
            }
        }
        $SERVER_API_KEY = 'AAAAtX5a_xg:APA91bFCW6XtWkj4OWmkEFLGruyjkcjSNaOpIpFkrWlbvyksPog2LaG08j8ZLiBbi8M3boxZouks9EKvYjDGtJzt27G4ZfkAco9jj_2LPiPwOd96KD_YuhYm0CohvgnT4IBsx4fy__Tk';
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => 'Nouveaux invitation',
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
        $users = User::all();
        foreach ($users as $value) {
            if($value->id != $user_id)
            {
                $tokens[] = $value->device_token;
            }
        }
        $SERVER_API_KEY = 'AAAAtX5a_xg:APA91bFCW6XtWkj4OWmkEFLGruyjkcjSNaOpIpFkrWlbvyksPog2LaG08j8ZLiBbi8M3boxZouks9EKvYjDGtJzt27G4ZfkAco9jj_2LPiPwOd96KD_YuhYm0CohvgnT4IBsx4fy__Tk';
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
}
