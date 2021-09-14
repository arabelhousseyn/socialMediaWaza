<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupUniverse;
use Illuminate\Support\Facades\Validator;
class GroupUniverseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(GroupUniverse::all(), 200);
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
            'name' => 'required',
            'cover' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $path = '';
            $folderPath = env('MAIN_PATH') . "groupUnivers/";
            $image_base64 = base64_decode($request->cover);
            $path = uniqid() . '.jpg';
            $file = $folderPath . $path;
            file_put_contents($file, $image_base64);
            
            GroupUniverse::create([
                'name' => $request->name,
                'cover' => $path
            ]);
            return response()->json(['success' => true], 200);
        }        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GroupUniverse  $groupUniverse
     * @return \Illuminate\Http\Response
     */
    public function show(GroupUniverse $groupUniverse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GroupUniverse  $groupUniverse
     * @return \Illuminate\Http\Response
     */
    public function edit(GroupUniverse $groupUniverse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GroupUniverse  $groupUniverse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GroupUniverse $groupUniverse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GroupUniverse  $groupUniverse
     * @return \Illuminate\Http\Response
     */
    public function destroy(GroupUniverse $groupUniverse)
    {
        //
    }
}
