<?php


namespace App\Traits;

use App\Models\Admin;

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
}
