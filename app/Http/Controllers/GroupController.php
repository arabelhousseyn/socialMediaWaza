<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\notification;
class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         /**
         * merge between the the first group of user and add special groups by id then other groups in the last of collection
         */

        $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100],['id','<>',99]])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100],['id','<>',99]])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $data3 = Group::where('id',100)->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $updatedItems = $data->merge($data3);
        $data->setCollection($updatedItems);
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
            $folderPath = env('MAIN_PATH') . "groupImages/";
            $path1 = '';
                $checkName = Group::where('name',$request->name)->first();
                if($checkName)
                {
                    return response()->json(['success' => false,'message' => 1], 200);
                }
            $image_base64 = base64_decode($request->cover);
            $path = uniqid() . '.jpg';
            $file = $folderPath . $path;
            file_put_contents($file, $image_base64);

            if(strlen($request->large_cover) != 0)
            {
            $image_base64 = base64_decode($request->large_cover);
            $path1 = uniqid() . '.jpg';
            $file = $folderPath . $path1;
            file_put_contents($file, $image_base64);
            }

            $group = Group::create([
                'name' => $request->name,
                'user_id' => Auth::user()->id,
                'cover' => $path,
                'type' => $request->type,
                'gender' => ($request->type == 0) ? $request->gender : null,
                'minAge' => ($request->type == 0) ? $request->minAge : null,
                'maxAge' => ($request->type == 0) ? $request->maxAge : null,
                'group_universe_id' => $request->group_universe_id,
                'large_cover' => $path1
            ]);

            $notification = notification::create([
                'user_id' => Auth::user()->id,
                'morphable_id' => $group->id,
                'type' => 3,
                'is_read' => 0
            ]);
            
            return response()->json(['success' => true,'id' => $group->id,'image' => $path,'notification_id' => $notification->id,'cover' => $path1], 200);
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
     * @return 
     * \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Group = Group::where('id',$id)->update($request);
        if($group)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
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
        /**
         * get groups by universe
         * merge between the the first group of user and the second of other groups
         */
        if($id == 0)
        {
            $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100],['id','<>',99]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100],['id','<>',99]])->select('id','name','cover')->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
        }

        $data = Group::where([['user_id','=',Auth::user()->id],['group_universe_id','=',$id],['id','<>',100],['id','<>',99]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['group_universe_id','=',$id],['id','<>',100],['id','<>',99]])->select('id','name','cover')->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }
}
