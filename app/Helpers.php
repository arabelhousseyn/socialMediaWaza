<?php

     function saveImage($image,$type)
    {
        switch($type)
        {
            case 'register' :
            $path = '';
        $folderPath = "storage/app/profiles/";
        $image_base64 = base64_decode($image);
        $path = uniqid() . '.jpg';
        $file = $folderPath . $path;
        file_put_contents($file, $image_base64);
        return $path;
             break;
             case 'amana' :
                $path = '';
        $folderPath = "storage/app/amanaImages/";
        $image_base64 = base64_decode($image);
        $path = uniqid() . '.jpg';
        $file = $folderPath . $path;
        file_put_contents($file, $image_base64);
        return $path;
                break;
        }
    }


    function distance($latitude1, $longitude1, $latitude2, $longitude2) {
        $theta = $longitude1 - $longitude2; 
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
        $distance = acos($distance); 
        $distance = rad2deg($distance); 
        $distance = $distance * 60 * 1.1515; 
        $distance = $distance * 1.609344; 
        return (round($distance,2)); 
      }