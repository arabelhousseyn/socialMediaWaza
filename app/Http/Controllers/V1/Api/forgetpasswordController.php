<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
Use App\Models\User;
use App\Mail\verificationMail;
use Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class forgetpasswordController extends Controller
{
    public function index(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'email' => 'required|email:rfc,dns,filter',
    ]);

    if($validator->fails())
    {
      return response()->json(['success' => false], 200);
    }

    if($validator->validated())
    {
            $user = User::where('email',$request->email)->first();
            if($user)
            {
                $code = uniqid();
                $message = "votre vérification de code " . $code;
                $details = [
                  "title" => "vérification de code WAZA",
                  "body" => $message
                           ];
          
              Mail::to($request->email)->send(new verificationMail($details));


                $user->update([
                    'code_verification' => $code
                ]);
                return response()->json(['success' => true,'user_id' => $user->id], 200);
            }
            return response()->json(['success' => false], 200);

    }
    }


    public function verify(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        if($user && $user->code_verification == $request->code)
        {
                User::where('id',$request->user_id)->update([
                    'password' => Hash::make($request->new_password),
                    'code_verification' => null,
                ]);
                return response()->json(['success' => true], 200);

        }
        return response()->json(['success' => false], 200);
    }

}
