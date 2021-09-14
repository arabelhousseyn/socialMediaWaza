<?php

namespace App\Http\Controllers;

use App\Models\Freelance;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
class FreelanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Freelance::orderBy('id','DESC')->select('id','description','user_id','job_offer_id')->paginate(20);
        foreach ($data as $value) {
            $user = User::where('id',$value->user_id)->first();
            $value['user'] = $user->fullName;
            $value['pictureUser'] = $user->picture;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }
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
            'job_offer_id' => 'required',
            'description' => 'required',
            'date' => 'required|date',
            'duration' => 'required',
            'area' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $freelance = Freelance::create([
                'user_id' => Auth::user()->id,
                'job_offer_id' => $request->job_offer_id,
                'description' => $request->description,
                'date' => $request->date,
                'duration' => $request->duration,
                'area' => $request->area
            ]);

            return response()->json(['success' => true], 200);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Freelance  $freelance
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Freelance::with('user')->find($id);
         if($data)
         {
            $data['pictureUser'] = $data->user->picture;
            $data['is_kaiztech_team'] = $data->user->is_kaiztech_team;
            return response()->json(['success' => true,'data' => $data], 200);
         }
         return response()->json(['success' => false], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freelance  $freelance
     * @return \Illuminate\Http\Response
     */
    public function edit(Freelance $freelance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freelance  $freelance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Freelance $freelance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freelance  $freelance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Freelance $freelance)
    {
        //
    }
}
