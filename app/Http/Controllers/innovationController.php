<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\innovation;
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
            'innovation_domain_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
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
                    $folderPath = "storage/app/ImageCompany/";
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
                    $folderPathImage = "storage/app/innovationImages/";
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
            $likeList = array();
            $dislikeList = array();
            
            $temp = $data->likesList;
            $data['pictureUser'] = $data->user->picture;
            $data['is_kaiztech_team'] = $data->user->is_kaiztech_team;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getInnovationByDomain($id)
    {
        $tempImages = array();
        if($id == 0)
        {
            $data = innovation::select('id','title','user_id','type','imageCompany')->orderBy('id','DESC')->paginate(20);
        
        foreach ($data as $value) {
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
            $value['pictureUser'] = $user->picture;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }
    
        return response()->json($data, 200);
        }

        $data = innovation::where('innovation_domain_id','=',$id)->orderBy('id','DESC')
->select('id','title','user_id','type','imageCompany')->paginate(20);

        foreach ($data as $value) {
            $userInnovation = innovation::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            $value['images'] = $userInnovation->images[0]->path;
            $value['user'] = $user->fullName;
            $value['pictureUser'] = $user->picture;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }

        return response()->json($data, 200);
    }



    public function funding(Request $request)
    {
        $pathPdf = '';
        if(strlen($request->pathBusinessPlan) != 0)
                 {
                   // $pdf = gzdecode(base64_decode($request->pathBusinessPlan));
                    $folderPath = "storage/app/bussinesPlan/";
                    $image_base64 = base64_decode($request->pathBusinessPlan);
                    $pathPdf = uniqid() . '.pdf';
                    $file = $folderPath . $pathPdf;
                    file_put_contents($file, $image_base64);
                 }
                 innovation::where('id',$request->id)->update([
                     'is_financed' => 1,
                     'financementAmount' => $request->financementAmount,
                     'pathBusinessPlan' => $pathPdf
                 ]);
                 
                 return response()->json(['success' => true], 200);
    }
  
}
