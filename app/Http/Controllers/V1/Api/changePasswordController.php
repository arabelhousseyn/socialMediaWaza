<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
class changePasswordController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "old_password" => 'required|min:8|max:255',
            "new_password" => 'required|min:8|max:255',
        ]);
        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }
        if($validator->validated())
        {
            $user = User::select('password')->find(Auth::user()->id);
            if($user)
            {
                if(Hash::check($request->old_password, $user->password))
                {
                   $updatePasswword = User::where('id',Auth::user()->id)->update([
                       'password' => Hash::make($request->new_password)
                   ]); 
                   if($updatePasswword)
                   {
                    return response()->json(['success' => true], 200);
                   }
                   return response()->json(['success' => false], 200);
                }
                return response()->json(['success' => false], 200);
            }
            return response()->json(['success' => false], 200);
        }
    }
}
