<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\followGroup;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\notification;
use App\Models\User;
use App\Traits\{
    SendNotification,
    upload,
    middlewares
};
use Carbon\Carbon;
class GroupController extends Controller
{
    use SendNotification,upload,middlewares;
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
        $ids = array();
        $count = Group::count();
        $count += 10;
        $user = User::find(Auth::user()->id);
        $age = Carbon::parse($user->dob)->age;

        $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100],['id','<>',161]])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $groups = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100],['id','<>',161]])->orderBy('id','DESC')->get();
        $data3 = Group::whereIn('id',[100,161])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);

        foreach ($groups as $group) {
            $check = $this->checkIfEligible($age,$user->gender,$group->id);
            if($check)
            {
                $ids[] = $group->id;
            }
         }
         $data2 = Group::whereIn('id',$ids)->select('id','name','cover')->inRandomOrder()->orderBy('id','DESC')->paginate($count);
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
        // insert group
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
                'cover' => env('DISPLAY_PATH') .'groupImages/'.$path,
                'type' => $request->type,
                'gender' => ($request->type == 0) ? $request->gender : null,
                'minAge' => ($request->type == 0) ? $request->minAge : null,
                'maxAge' => ($request->type == 0) ? $request->maxAge : null,
                'group_universe_id' => $request->group_universe_id,
                'large_cover' => (strlen($path1) != 0) ? env('DISPLAY_PATH') .'groupImages/'.$path1
                : ''
            ]);
            $cover = (strlen($path1) != 0) ? env('DISPLAY_PATH') .'groupImages/'. $path1 : ''; 

            $notification = notification::create([
                'user_id' => Auth::user()->id,
                'morphable_id' => $group->id,
                'type' => 3,
                'is_read' => 0
            ]);
            return response()->json(['success' => true,'id' => $group->id,'image' => env('DISPLAY_PATH') .'groupImages/'. $path,'notification_id' => $notification->id,'cover' => $cover], 200);
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
        // show details of group
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
        // to be changed
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
        // delete group
        $Group = Group::where('id',$id)->delete();
        if($Group)
        {
            notification::where([['morphable_id','=',$id],['type','=',3]])->delete();
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
        // get all groups
        $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100]])->select('id','name','cover')->inRandomOrder()->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
        }
        // get groups by univers
        $data = Group::where([['user_id','=',Auth::user()->id],['group_universe_id','=',$id],['id','<>',100]])->select('id','name','cover')->paginate(20);
        $data2 = Group::where([['user_id','<>',Auth::user()->id],['group_universe_id','=',$id],['id','<>',100]])->select('id','name','cover')->inRandomOrder()->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }

    public function searchGroup($name = null)
    {
        // search for group
        if($name == null)
       {
           return response()->json([], 200);
       }else{
        if(strlen($name) >= 3)
        {
            $data = Group::where('name', 'LIKE', "%{$name}%")->get();
        return response()->json($data, 200);
        }
        return response()->json([], 200);
       }
    }


    public function infoGroup($group_id = null)
    {
        // get innformatiom about group
        $final = array();
        $data = Group::find($group_id);
        if($data)
        {
            $countNumberFollowers = followGroup::where('follow_id',$group_id)->count();
            $final['cover'] = (strlen($data->large_cover) != 0) ? $data->large_cover : '';
            $final['countNumberFollowers'] = $countNumberFollowers;
            return response()->json($final, 200);
        }
        return response()->json(['success' => false], 200);
    }
    // v2

    public function getOwnGroups()
    {
        $data = Group::where([['user_id','=',Auth::user()->id],['id','<>',100],['id','<>',161]])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        $data3 = Group::whereIn('id',[100,161])->select('id','name','cover')->orderBy('id','DESC')->paginate(20);
        
        $updatedItems = $data->merge($data3);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }

    public function getRandomGroups()
    {
        $user = User::find(Auth::user()->id);
        $age = Carbon::parse($user->dob)->age;
        $groups = Group::where([['user_id','<>',Auth::user()->id],['id','<>',100],['id','<>',161]])->orderBy('id','DESC')->get();
        foreach ($groups as $group) {
            $check = $this->checkIfEligible($age,$user->gender,$group->id);
            if($check)
            {
                $ids[] = $group->id;
            }
         }
        $data2 = Group::whereIn('id',$ids)->select('id','name','cover')->inRandomOrder()->orderBy('id','DESC')->paginate(20);
        return response()->json($data2, 200);
    }
}
