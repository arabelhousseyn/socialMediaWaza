<?php

namespace App\Http\Controllers;

use App\Models\Amana;
use Illuminate\Http\Request;
use App\Models\AmanaImage;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Validator;
class AmanaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Amana::with('images','user')->orderBy('id','DESC')->paginate(20);
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
            'title' => 'required|max:255',
            'description' => 'required',
            'abbreviation' => 'required',
            'images' => 'required',
            'amana_category_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $amana = Amana::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::user()->id,
                'abbreviation' => $request->abbreviation,
                'amana_category_id' => $request->amana_category_id,
            ]);

            $images = explode(';ibaa;',$request->images);
            
            foreach ($images as $image) {
                $path = '';
        $folderPath = env('MAIN_PATH') . "amanaImages/";
        $image_base64 = base64_decode($image);
        $path = uniqid() . '.jpg';
        $file = $folderPath . $path;
        file_put_contents($file, $image_base64);

            AmanaImage::create([
                'amana_id' => $amana->id,
                'path' => $path
            ]);
            }
            return response()->json(['success' => true], 200);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Amana  $amana
     * @return \Illuminate\Http\Response
     */
    public function show(Amana $amana)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Amana  $amana
     * @return \Illuminate\Http\Response
     */
    public function edit(Amana $amana)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Amana  $amana
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Amana $amana)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Amana  $amana
     * @return \Illuminate\Http\Response
     */
    public function destroy(Amana $amana)
    {
        //
    }

    public function amanaByCategory($id)
    { $tempImages = array();
        if($id == 0)
        {
            $data = Amana::select('id','title','user_id','description','abbreviation','amana_category_id','created_at')->orderBy('id','DESC')->paginate(20);
            foreach ($data as $value) {
                $userAmana = Amana::with('images')->where('id',$value->id)->first();
                $user = User::where('id',$value->user_id)->first();

                if(count($userAmana->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userAmana->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userAmana->images;
            }
            $value['countImages'] = count($userAmana->images);
                $value['user'] = $user->fullName;
                $value['pictureUser'] = $user->picture;
                $value['is_kaiztech_team'] = $user->is_kaiztech_team;
                $value['user_profession'] = $user->profession;
            }
        return response()->json($data, 200);
        }

        $data = Amana::where('amana_category_id',$id)->select('id','title','user_id','description','abbreviation','amana_category_id','created_at')->orderBy('id','DESC')->paginate(20);
        foreach ($data as $value) {
            $userAmana = Amana::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();

            if(count($userAmana->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userAmana->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userAmana->images;
            }
            $value['countImages'] = count($userAmana->images);
            $value['user'] = $user->fullName;
            $value['pictureUser'] = $user->picture;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
            $value['user_profession'] = $user->profession;
        }
        return response()->json($data, 200);

    }
}