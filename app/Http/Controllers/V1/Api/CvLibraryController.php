<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\CvLibrary;
use App\Models\Experience;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\upload;
class CvLibraryController extends Controller
{
    use upload;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = CvLibrary::select('id','path','user_id')->orderBy('id','DESC')->paginate(20);
        foreach ($data as $value) {
            $user = User::where('id',$value->user_id)->first();
            $value['user'] = $user->fullName;
            $value['pictureUser'] = env('DISPLAY_PATH') . 'profiles/' . $user->picture;
            $value['path'] = env('DISPLAY_PATH') . 'CvLibraryImages/' . $value->path;
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
            $value['profession'] = $user->profession;
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
            'FullName' => 'required|max:255',
            'dob' => 'required|date',
            'arabic' => 'required',
            'english' => 'required',
            'french' => 'required',
            'phone' => 'required|digits:10',
            'email' => 'required|email:rfc,dns,filter',
            'area' => 'required',
            'description' => 'required'
        ]);

        if($validator->fails())
        {
            return response ()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            if(strlen($request->image) != 0)
            {
                $image = $this->ImageUpload($request->image,'CvLibraryImages');
            }

            $cvLibrary = CvLibrary::create([
                'user_id' => Auth::user()->id,
                'FullName' => $request->FullName,
                'path' => (strlen($image) != 0) ?$image : Auth::user()->picture,
                'dob' => $request->dob,
                'arabic' => $request->arabic,
                'english' => $request->english,
                'french' => $request->french,
                'phone' => $request->phone,
                'email' => $request->email,
                'area' => $request->area,
                'description' => $request->description
            ]);
            $experience = $request->experience;
            if(@count(@$experience) > 0)
            {
                foreach ($experience as $value) {
                    Experience::create([
                        'cv_library_id' => $cvLibrary->id,
                        'text' => $value['text'],
                        'grade' => $value['grade'],
                        'from' => $value['from'],
                        'to' => $value['to'],
                    ]);
                }
            }

            if($cvLibrary)
            {
                return response()->json(['success' => true], 200);
            }

            return response()->json(['success' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CvLibrary  $cvLibrary
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = CvLibrary::with('experiences')->find($id);
         if($data)
         {
            $user = User::where('id',$data->user_id)->first();

            $data['pictureUser'] = env('DISPLAY_PATH') . 'profiles/' . $user->picture;
            $data['is_kaiztech_team'] = $user->is_kaiztech_team;
            $data['profession'] = $user->profession;
            $data['path'] = env('DISPLAY_PATH') . 'CvLibraryImages/' . $data->path;
            return response()->json(['success' => true,'data' => $data], 200);
         }
         return response()->json(['success' => false], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CvLibrary  $cvLibrary
     * @return \Illuminate\Http\Response
     */
    public function edit(CvLibrary $cvLibrary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CvLibrary  $cvLibrary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CvLibrary $cvLibrary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CvLibrary  $cvLibrary
     * @return \Illuminate\Http\Response
     */
    public function destroy(CvLibrary $cvLibrary)
    {
        //
    }
}
