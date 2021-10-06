<?php

namespace App\Traits;


trait upload{

    public function ImageUpload($base64,$extension)
    {
        $path = '';
        $folderPath = env('MAIN_PATH') . $extension . '/';
        $image_base64 = base64_decode($base64);
        $path = uniqid() . '.jpg';
        $file = $folderPath . $path;
        file_put_contents($file, $image_base64);
        return $path;
    }

    public function PdfUpload($base64,$extension)
    {
        $pathPdf = '';
        $folderPath = env('MAIN_PATH') . $extension . '/';
        $pdf_base64 = base64_decode($base64);
        $pathPdf = uniqid() . '.pdf';
        $file = $folderPath . $pathPdf;
        file_put_contents($file, $pdf_base64);
        return $pathPdf;
    }
    
    public function VideoUpload($base64,$extension)
    {
        $videoPath = '';
        $folderPath = env('MAIN_PATH') . $extension . '/';
        $video_base64 = base64_decode($base64);
        $videoPath = uniqid() . '.mp4';
        $file = $folderPath . $videoPath;
        file_put_contents($file, $video_base64);
        return $videoPath;
    }

}