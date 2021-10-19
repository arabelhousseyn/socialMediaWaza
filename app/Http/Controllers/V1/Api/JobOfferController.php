<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\JobOffer;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\upload;
class JobOfferController extends Controller
{
    use upload;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = JobOffer::select('id','path','profile','user_id')->orderBy('id','DESC')->paginate(20);
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
        $image = '';

        $validator = Validator::make($request->all(), [
            'name_company' => 'required|max:255',
            'sector' => 'required',
            'address' => 'required',
            'job' => 'required',
            'status' => 'required',
            'state' => 'required',
            'description' => 'required',
            'mission' => 'required',
            'profile' => 'required|unique:job_offers',
            'advantage' => 'required'
        ]);
        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            if(strlen($request->image) != 0)
            {
                $image = $this->ImageUpload($request->image,'JobOffersImages');
            }

            $jobOffer = JobOffer::create([
                'user_id' =>Auth::user()->id,
                'name_company' => $request->name_company,
                'path' => (strlen($image) != 0) ? 'https://dev.waza.fun/JobOffersImages/'. $image 
                : Auth::user()->picture,
                'sector' => $request->sector,
                'address' => $request->address,
                'job' => $request->job,
                'status' => $request->status,
                'state' => $request->state,
                'price' => $request->price,
                'description' => $request->description,
                'mission' => $request->mission,
                'profile' => $request->profile,
                'advantage' => $request->advantage,
            ]);

            if($jobOffer)
            {
                return response()->json(['success' => true], 200);
            }
            return response()->json(['success' => true], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = JobOffer::find($id);
        if($data)
        {
            $user = User::where('id',$data->user_id)->first();

            $data['pictureUser'] = $user->picture;
            $data['is_kaiztech_team'] = $user->is_kaiztech_team;
            $data['profession'] = $user->profession;
           return response()->json(['success' => true,'data' => $data], 200);
        }
        return response()->json(['success' => false], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return \Illuminate\Http\Response
     */
    public function edit(JobOffer $jobOffer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobOffer  $jobOffer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobOffer $jobOffer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobOffer $jobOffer)
    {
        //
    }

    public function getAll()
    {
        return response()->json(JobOffer::select('id','profile')->get(), 200);
    }
}
