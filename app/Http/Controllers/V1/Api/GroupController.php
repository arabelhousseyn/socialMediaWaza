<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\notification;
use App\Models\{GroupPost, User, followGroup};
use App\Traits\{
    SendNotification,
    upload,
    middlewares
};
use Carbon\Carbon;

class GroupController extends Controller
{
    use SendNotification, upload, middlewares;

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

        $data = Group::where([['user_id', '=', Auth::user()->id], ['id', '<>', 100], ['id', '<>', 161]])->select('id', 'name', 'logo')->orderBy('id', 'DESC')->paginate(20);
        $groups = Group::where([['user_id', '<>', Auth::user()->id], ['id', '<>', 100], ['id', '<>', 161]])->orderBy('id', 'DESC')->get();
        $data3 = Group::whereIn('id', [100, 161])->select('id', 'name', 'logo')->orderBy('id', 'DESC')->paginate(20);

        foreach ($groups as $group) {
            $check = $this->checkIfEligible($age, $user->gender, $group->id);
            if ($check) {
                $ids[] = $group->id;
            }
        }
        $data2 = Group::whereIn('id', $ids)->select('id', 'name', 'logo')->inRandomOrder()->orderBy('id', 'DESC')->paginate($count);
        foreach ($data2 as $value) {
            $value['cover'] = $value->logo;
        }
        foreach ($data3 as $value) {
            $value['cover'] = $value->logo;
        }
        foreach ($data as $value) {
            $value['cover'] = $value->logo;
        }
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
     * @param \Illuminate\Http\Request $request
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

        if ($validator->fails()) {
            return response()->json(['success' => false], 200);
        }

        if ($validator->validated()) {
            $path1 = '';
            $checkName = Group::where('name', $request->name)->first();
            if ($checkName) {
                return response()->json(['success' => false, 'message' => 1], 200);
            }

            $path = $this->ImageUpload($request->cover, 'groupImages');

            if (strlen($request->large_cover) != 0) {
                $path1 = $this->ImageUpload($request->large_cover, 'groupImages');
            }

            $group = Group::create([
                'name' => $request->name,
                'user_id' => Auth::user()->id,
                'logo' => env('DISPLAY_PATH') . 'groupImages/' . $path,
                'type' => $request->type,
                'description' => ($request->description) ? $request->description : '',
                'gender' => ($request->type == 0) ? $request->gender : null,
                'minAge' => ($request->type == 0) ? $request->minAge : null,
                'maxAge' => ($request->type == 0) ? $request->maxAge : null,
                'group_universe_id' => $request->group_universe_id,
                'large_cover' => (strlen($path1) != 0) ? env('DISPLAY_PATH') . 'groupImages/' . $path1
                    : ''
            ]);
            $cover = (strlen($path1) != 0) ? env('DISPLAY_PATH') . 'groupImages/' . $path1 : '';

            $notification = notification::create([
                'user_id' => Auth::user()->id,
                'morphable_id' => $group->id,
                'type' => 3,
                'is_read' => 0
            ]);
            return response()->json(['success' => true, 'id' => $group->id, 'image' => env('DISPLAY_PATH') . 'groupImages/' . $path, 'notification_id' => $notification->id, 'cover' => $cover], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // show details of group
        $Group = Group::findOrFail($id);
        return response()->json(['success' => true, $Group], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return
     * \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // to be changed
        $Group = Group::where('id', $id)->update($request);
        if ($group) {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete group
        $Group = Group::where('id', $id)->delete();
        if ($Group) {
            notification::where([['morphable_id', '=', $id], ['type', '=', 3]])->delete();
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
        if ($id == 0) {
            // get all groups
            $data = Group::where([['user_id', '=', Auth::user()->id], ['id', '<>', 100]])->select('id', 'name', 'logo')->paginate(20);
            $data2 = Group::where([['user_id', '<>', Auth::user()->id], ['id', '<>', 100]])->select('id', 'name', 'logo')->inRandomOrder()->paginate(20);
            $updatedItems = $data->merge($data2);
            $data->setCollection($updatedItems);
            return response()->json($data, 200);
        }
        // get groups by univers
        $data = Group::where([['user_id', '=', Auth::user()->id], ['group_universe_id', '=', $id], ['id', '<>', 100]])->select('id', 'name', 'logo')->paginate(20);
        $data2 = Group::where([['user_id', '<>', Auth::user()->id], ['group_universe_id', '=', $id], ['id', '<>', 100]])->select('id', 'name', 'logo')->inRandomOrder()->paginate(20);
        $updatedItems = $data->merge($data2);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }

    public function searchGroup($name = null)
    {
        // search for group
        if ($name == null) {
            return response()->json([], 200);
        } else {
            if (strlen($name) >= 3) {
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
        if ($data) {
            $countNumberFollowers = followGroup::where('follow_id', $group_id)->count();
            $final['cover'] = (strlen($data->large_cover) != 0) ? $data->large_cover : '';
            $final['countNumberFollowers'] = $countNumberFollowers;
            return response()->json($final, 200);
        }
        return response()->json(['success' => false], 200);
    }

    // v2

    public function getOwnGroups()
    {
        $ids = array();
        $data = Group::where('user_id', Auth::user()->id)->select('id', 'name', 'logo')->orderBy('id', 'DESC')->paginate(20);
        $data3 = followGroup::where('user_id', Auth::user()->id)->get();
        foreach ($data3 as $value) {
            $ids[] = $value->follow_id;
        }

        $data3 = Group::whereIn('id', $ids)->select('id', 'name', 'logo')->orderBy('id', 'DESC')->paginate(20);
        foreach ($data as $value) {
            $value['is_own'] = 1;
        }

        foreach ($data3 as $value) {
            $value['is_own'] = 0;
        }
        $updatedItems = $data->merge($data3);
        $data->setCollection($updatedItems);
        return response()->json($data, 200);
    }

    public function getRandomGroups()
    {
        $ids = array();
        $ids2 = array();
        $user = User::find(Auth::user()->id);
        $age = Carbon::parse($user->dob)->age;
        $groups = Group::where([['user_id', '<>', Auth::user()->id], ['id', '<>', 161]])->orderBy('id', 'DESC')->get();
        foreach ($groups as $group) {
            $check = $this->checkIfEligible($age, $user->gender, $group->id);
            if ($check) {
                $ids[] = $group->id;
            }
        }
        $data2 = Group::whereIn('id', $ids)->select('id', 'name', 'logo')->inRandomOrder()->orderBy('id', 'DESC')->paginate(20);
        foreach ($data2 as $value) {
            $ids2[] = $value->id;
        }
        $data3 = Group::whereIn('id', $ids2)->select('id', 'name', 'logo')->orderBy('id', 'DESC')->paginate(20);
        return response()->json($data3, 200);
    }

    public function getGroupInformation($id)
    {
        $group = Group::with('linkInformation')->find($id, ['name', 'logo', 'large_cover', 'description']);
        $group_followers_count = followGroup::where('follow_id', $id)->count();
        $is_followed = followGroup::where('user_id', Auth::id())->count();
        return response()->json([
            'group_name' => $group->name,
            'group_logo' => $group->logo,
            'group_large_cover' => $group->large_cover,
            'group_description' => $group->description,
            'group_followers_count' => $group_followers_count,
            'is_followed' => $is_followed === 1,
        ], 200);
    }
}
