<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\followGroup;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\notification;
use App\Traits\SendNotification;
use App\Traits\upload;
class GroupController extends Controller
{
    use SendNotification,upload;
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

        $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100]])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100]])->select('id','name','cover')->inRandomOrder()->orderBy('id','DESC')->paginate(20);
        $data3 = Group::whereIn('id',[100,161])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
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
            $path1 = '';
                $checkName = Group::where('name',$request->name)->first();
                if($checkName)
                {
                    return response()->json(['success' => false,'message' => 1], 200);
                }

            $path = $this->ImageUpload($request->cover,'groupImages');

            if(strlen($request->large_cover) != 0)
            {
            $path1 = $this->ImageUpload($request->large_cover,'groupImages');
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
            $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100]])->select('id','name','cover')->inRandomOrder()->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
        }

        $data = Group::where([['user_id','=',Auth::user()->id],['group_universe_id','=',$id],['id','<>',100]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['group_universe_id','=',$id],['id','<>',100]])->select('id','name','cover')->inRandomOrder()->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }

    public function searchGroup($name = null)
    {
        if($name == null)
       {
           return response()->json([], 200);
       }else{
        if(strlen($name) >= 3)
        {
            $data = Group::where('name', 'LIKE', "%{$name}%")->get();
        foreach ($data as $value) {
            $value->cover = env('DISPLAY_PATH') . 'groupImages/' . $value->cover;
            (strlen($value->large_cover) != 0) ? 
            $value->large_cover = env('DISPLAY_PATH') . 'groupImages/' . $value->large_cover : '';
        }
        return response()->json($data, 200);
        }
        return response()->json([], 200);
       }
    }


    public function infoGroup($group_id = null)
    {
        $final = array();
        $data = Group::find($group_id);
        if($data)
        {
            $countNumberFollowers = followGroup::where('follow_id',$group_id)->count();
            $final['cover'] = (strlen($data->large_cover) != 0) ? 
              env('DISPLAY_PATH') . 'groupImages/' . $data->large_cover : '';
            $final['countNumberFollowers'] = $countNumberFollowers;
            return response()->json($final, 200);
        }
        return response()->json(['success' => false], 200);
    }
}
