<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Auth;
use Illuminate\Support\Facades\Validator;
class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Group::where('user_id','=',Auth::user()->id)->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $data2 = Group::where('user_id','<>',Auth::user()->id)->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
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
            'name' => 'required|max:255',
            'cover' => 'required',
            'type' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            if($request->type == 1)
            {
                $checkName = Group::where('name',$request->name)->first();
                if($checkName)
                {
                    return response()->json(['success' => false,'message' => 1], 200);
                }

            $folderPath = "storage/app/groupImages/";
            $image_base64 = base64_decode($request->cover);
            $path = uniqid() . '.jpg';
            $file = $folderPath . $path;
            file_put_contents($file, $image_base64);

            $group = Group::create([
                'name' => $request->name,
                'user_id' => Auth::user()->id,
                'cover' => $path,
                'type' => $request->type,
                'gender' => $request->gender,
                'minAge' => $request->minAge,
                'maxAge' => $request->maxAge,
                'group_universe_id' => $request->group_universe_id,
            ]);

            return response()->json(['success' => true,'id' => $group->id,'image' => $path], 200);
            }

            $checkName = Group::where('name',$request->name)->first();
                if($checkName)
                {
                    return response()->json(['success' => false,'message' => 1], 200);
                }

            $folderPath = "storage/app/groupImages/";
            $image_base64 = base64_decode($request->cover);
            $path = uniqid() . '.jpg';
            $file = $folderPath . $path;
            file_put_contents($file, $image_base64);

            $group = Group::create([
                'name' => $request->name,
                'user_id' => Auth::user()->id,
                'cover' => $path,
                'type' => $request->type,
                'gender' => null,
                'minAge' => null,
                'maxAge' => null,
                'group_universe_id' => $request->group_universe_id,
            ]);

            return response()->json(['success' => true,'id' => $group->id,'image' => $path], 200);
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
        $Group = Group::findOrFail($id);
        return response()->json(['success' =>true,$Group], 200);
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
        $Group = Group::where('id',$id)->update($request);
        if($group)
        {
            return response()->json(['success' => true], 200, $headers);
        }
        return response()->json(['success' => false], 200, $headers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Group = Group::where('id',$id)->delete();
        if($Group)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
    }

    public function getgroupsByunivers($id)
    {
        if($id == 0)
        {
            $data = Group::where('user_id','=',Auth::user()->id)->select('id','name','cover')->paginate(20);
        $data2 = Group::where('user_id','<>',Auth::user()->id)->select('id','name','cover')->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
        }

        $data = Group::where([['user_id','=',Auth::user()->id],['group_universe_id','=',$id]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['group_universe_id','=',$id]])->select('id','name','cover')->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }
}
