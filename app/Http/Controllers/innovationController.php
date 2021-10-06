<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\innovation;
use App\Models\innovationDomain;
use App\Models\innovationImage;
use App\Models\innovationLike;
use App\Models\User;
use Carbon\Carbon;
use ImageOptimizer;
use Auth;
use Illuminate\Support\Facades\Validator;
class innovationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = innovation::with('likesList')
        ->whereDate('created_at', '>=', Carbon::now()->subDays(7)->setTime(0, 0, 0)->toDateTimeString())->paginate(7);
        return response()->json($data, 200);
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
            'title' => 'required',
            'description' => 'required',
            'innovation_domain_id' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $checkInnovationDomain = innovationDomain::find($request->innovation_domain_id);

            if(!$checkInnovationDomain)
            {
                return response()->json(['success' => false], 200);
            }

            $check = false;
            $innovation = null;
            $pathAudio = null;
            $pathImageCompany = '';
                 if(strlen($request->audio) != 0)
                 {
                    $pathAudio = $request->audio;
                 }

                 if(strlen($request->imageCompany) != 0)
                 {
                    $folderPath = env('MAIN_PATH') . "ImageCompany/";
                    $image_base64 = base64_decode($request->imageCompany);
                    $pathImageCompany = uniqid() . '.jpg';
                    $file = $folderPath . $pathImageCompany;
                    file_put_contents($file, $image_base64);
                 }

                    $innovation = innovation::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'audio' => $pathAudio,
                        'is_financed' => 0,
                        'financementAmount' => 0,
                        'pathBusinessPlan' => '',
                        'user_id' => Auth::user()->id,
                        'likes' => 0,
                        'type' => $request->type,
                        'imageCompany' =>$pathImageCompany,
                        'innovation_domain_id' => $request->innovation_domain_id,
                    ]);

                   $images = explode(';ibaa;',$request->images);
                   foreach ($images as $image) {
                    $pathImage = uniqid() . '.jpg';
                    $folderPathImage = env('MAIN_PATH') . "innovationImages/";
                    $image_base64 = base64_decode($image);
                    $file = $folderPathImage . $pathImage;
                    file_put_contents($file, $image_base64);
                           $check = innovationImage::create([
                               'path' => $pathImage,
                               'innovation_id' => $innovation->id,
                           ]);
                   }

                   if($check)
                   {
                       return response()->json(['success' => true,'id' => $innovation->id], 200);
                   }
                   return response()->json(['success' => false], 200);
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
         $data = innovation::with('user','images','likesList')->find($id);
         if($data)
         {
            $user = User::where('id',$data->user_id)->first();
            $likeList = array();
            $dislikeList = array();
            
            $temp = $data->likesList;
            if(strlen($data->imageCompany) != 0)
            {
                $data['pictureUser'] = env('DISPLAY_PATH') . 'ImageCompany/' . $data->imageCompany;
                $data['is_company'] = 1;
            }else{
                $data['pictureUser'] = env('DISPLAY_PATH') . 'profiles/' . $user->picture;
                $data['is_company'] = 0;
            }
            $data['is_kaiztech_team'] = $user->is_kaiztech_team;
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

            if(strlen($data->pathBusinessPlan) != 0)
            {
                $data['pathBusinessPlan'] = env('DISPLAY_PATH') . 'bussinesPlan/' . $data->pathBusinessPlan;
            }

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
        $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $GroupPost = innovation::where('id',$id)->update([
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
        $GroupPost = innovation::where('id',$id)->delete();
        if($GroupPost)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
    }

    public function getInnovationByDomain($id)
    {
        if($id == 0)
        {
            $data = innovation::with('likesList')->selective($id)->paginate(20);
        
        foreach ($data as $value) {
            $value['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $tempImages = array();
            $likeList = array();
            $dislikeList = array();
            $temp = $value->likesList;
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
            $value['dislikes'] = count($dislikeList);
            $value['likes'] = count($likeList);

            $userInnovation = innovation::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            if(count($userInnovation->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userInnovation->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userInnovation->images;
            }
            $value['countImages'] = count($userInnovation->images);
            $value['user'] = $user->fullName;
            if(strlen($value->imageCompany) != 0)
            {
                $value['pictureUser'] = env('DISPLAY_PATH') . 'ImageCompany/' . $value->imageCompany;
            }else{
                $value['pictureUser'] = env('DISPLAY_PATH') . 'profiles/'.$user->picture;
            }
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }
    
        return response()->json($data, 200);
        }

        $data = innovation::with('likesList')->selective($id)->paginate(20);

        foreach ($data as $value) {
            $value['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $tempImages = array();
            $likeList = array();
            $dislikeList = array();
            $temp = $value->likesList;
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
            $value['dislikes'] = count($dislikeList);
            $value['likes'] = count($likeList);
            $userInnovation = innovation::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            if(count($userInnovation->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userInnovation->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userInnovation->images;
            }
            $value['countImages'] = count($userInnovation->images);
            $value['user'] = $user->fullName;
            if(strlen($value->imageCompany) != 0)
            {
                $value['pictureUser'] = env('DISPLAY_PATH') . 'ImageCompany/' . $value->imageCompany;
            }else{
                $value['pictureUser'] = env('DISPLAY_PATH') . 'profiles/' . $user->picture;
            }
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }

        return response()->json($data, 200);
    }



    public function funding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pathBusinessPlan' => 'required',
            'id' => 'required',
            'financementAmount' => 'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $pathPdf = '';
        if(strlen($request->pathBusinessPlan) != 0)
                 {
                   // $pdf = gzdecode(base64_decode($request->pathBusinessPlan));
                    $folderPath = env('MAIN_PATH') . "bussinesPlan/";
                    $image_base64 = base64_decode($request->pathBusinessPlan);
                    $pathPdf = uniqid() . '.pdf';
                    $file = $folderPath . $pathPdf;
                    file_put_contents($file, $image_base64);
                 }
                $update = innovation::where('id',$request->id)->update([
                     'is_financed' => 1,
                     'financementAmount' => $request->financementAmount,
                     'pathBusinessPlan' => $pathPdf
                 ]);

                 if(!$update)
                 {
                     return response()->json(['success' => false], 200);
                 }
                 
                 return response()->json(['success' => true], 200);
        }
    }

    public function handleActionInnovation(Request $request)
    {
        $check = innovationLike::where([['user_id','=',Auth::user()->id],['innovation_id','=',$request->innovation_id]])->first();

        if($check)
        {
            if($request->type == $check->type)
            {
                innovationLike::where([['user_id','=',Auth::user()->id],['innovation_id','=',$request->innovation_id]])->delete();  
            }else{
                $check->update([
                    'type' => $request->type
                ]);    
            }
        $data = $this->likeListByInnovation($request->innovation_id);

        return response()->json($data, 200);
        }

        $like = innovationLike::create([
            'user_id' => Auth::user()->id,
            'innovation_id' => $request->innovation_id,
            'type' => $request->type
        ]);

        $group_post = innovation::findOrFail($request->innovation_id);
        $likes = $group_post->likes + 1;
        $group_post->update([
            'likes' => $likes
        ]); 
        $data = $this->likeListByInnovation($request->innovation_id);

        return response()->json($data, 200);
    }

    public function likeListByInnovation($id)
    {
        $data = innovation::with('user','likesList')->find($id);
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
  
}
