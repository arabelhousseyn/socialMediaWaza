<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\innovationDomain;
use Illuminate\Support\Facades\Validator;
class innovationdomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = innovationDomain::all();
        return response()->json(['data' => $data], 200);
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
            'image' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $checkTitle = innovationDomain::where('title',$request->title)->first(); // to be changed
            if($checkTitle)
            {
                return response()->json(['success' => false,'message' => 1], 200);
            }

            $folderPath = env('MAIN_PATH') . "innovationDomainImages/";
            $image_base64 = base64_decode($request->image);
            $path = uniqid() . '.jpg';
            $file = $folderPath . $path;
            file_put_contents($file, $image_base64);

                $domain = innovationDomain::create([
                    'title' => $request->title,
                    'image' => $path,
                    'type' => 0,
                ]);

            return response()->json(['success' => true], 201);
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
        //
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
}
