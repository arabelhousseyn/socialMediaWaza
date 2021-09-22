<?php

namespace App\Http\Controllers;

use App\Models\followGroup;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
class FollowGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'group_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $checkGroup = Group::where('id',$request->group_id)->first();
        if($checkGroup)
        {

            $check = followGroup::where([['user_id','=',Auth::user()->id],['follow_id','=',$request->group_id]])->first();
            if($check)
            {
                $delete = followGroup::where('id',$check->id)->delete();
                if($delete)
                {
                    return response()->json(['success' => true,'following' => 0], 200);
                }
                return response()->json(['success' => false], 200);
            }else{
                $following = followGroup::create([
                    'user_id' => Auth::user()->id,
                    'follow_id' => $request->group_id
                ]);   
                if($following)
                {
                    return response()->json(['success' => true,'following' => 1], 200);
                }
            }
          return response()->json(['success' => false], 200);
        }
        return response()->json(['success' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\followGroup  $followGroup
     * @return \Illuminate\Http\Response
     */
    public function show(followGroup $followGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\followGroup  $followGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(followGroup $followGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\followGroup  $followGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, followGroup $followGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\followGroup  $followGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(followGroup $followGroup)
    {
        //
    }
}
