<?php

namespace App\Http\Controllers;

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
                // to be changed
                // $to = $request->email;
                // $subject = "Code vèrification";
                // $headers = "MIME-Version: 1.0" . "\r\n";
                // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                // $headers .= 'From: waza' . "\r\n";
                // $message = 'Voici votre code de confirmation : ' . $code;

                // mail($to,$subject,$message,$headers);


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

    public function message($code)
    {
        $message = '<div style="display: flex !important; flex-direction: row !important; justify-content: center !important; pointer-events: none !important; width: 100% !important;" class="container">
        <div style="background-color: #F6FBFF; box-shadow: 0px 3px 6px #00000029; height: 500px; overflow: hidden; width: 900px;" class="web screen">
            <div style="display: flex;flex-direction: row;" class="svgs">
                <div style="position: relative;right: 60px;top: -45px;" class="svg1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="300" height="300" viewBox="0 0 666.486 542.916">
                        <defs>
                          <filter id="Tracé_393536" x="0" y="0" width="666.486" height="542.916" filterUnits="userSpaceOnUse">
                            <feOffset dy="3" input="SourceAlpha"/>
                            <feGaussianBlur stdDeviation="3" result="blur"/>
                            <feFlood flood-opacity="0.161"/>
                            <feComposite operator="in" in2="blur"/>
                            <feComposite in="SourceGraphic"/>
                          </filter>
                        </defs>
                        <g transform="matrix(1, 0, 0, 1, 0, 0)" filter="url(#Tracé_393536)">
                          <path id="Tracé_393536-2" data-name="Tracé 393536" d="M-18006.061-18072.727s493.941-63.637,390.91-306.061,145.455-218.182,145.455-218.182h-648.486Z" transform="translate(18127.18 18603.64)" fill="#5eadfa"/>
                        </g>
                      </svg>                  
                </div>  
                <div class="svg2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="300" height="230" viewBox="0 0 306.165 310.485">
                        <g id="positive-vote" transform="translate(-0.5 0)">
                          <path id="Tracé_393538" data-name="Tracé 393538" d="M281.02,189.676A140.26,140.26,0,1,1,140.758,49.413,140.263,140.263,0,0,1,281.02,189.676Zm0,0" transform="translate(0 -19.448)" fill="#937de2"/>
                          <path id="Tracé_393539" data-name="Tracé 393539" d="M217.64,49.414a141.3,141.3,0,0,0-21.8,1.689,140.277,140.277,0,0,1,.01,277.14A140.265,140.265,0,1,0,217.64,49.414Zm0,0" transform="translate(-76.883 -19.448)" fill="#7570d6"/>
                          <path id="Tracé_393540" data-name="Tracé 393540" d="M368.883,159.859l-80.092,53.035a27.276,27.276,0,0,1-19.374,4.2,27.367,27.367,0,0,0-19.422,4.188l-10.719,7.1L179.25,139.9l14.727-63.622a66.407,66.407,0,0,0-9.328-51.638A15.877,15.877,0,0,1,208.936,4.48l9.542,9.241a101.968,101.968,0,0,1,24.612,37.73l5.173,13.929,53.277-35.2a18.167,18.167,0,0,1,25.453,5.545,18.405,18.405,0,0,1-5.706,24.953.2.2,0,0,0,.185.353,18.078,18.078,0,0,1,6.493-1.2,18.268,18.268,0,0,1,9.9,2.916A18.17,18.17,0,0,1,345.8,81.518a18.557,18.557,0,0,1-7.616,11.5.392.392,0,0,0-.1.54.015.015,0,0,0,.012.012.386.386,0,0,0,.512.1,18.644,18.644,0,0,1,4.408-.521,19.137,19.137,0,0,1,10.247,2.97,17.865,17.865,0,0,1,5.3,5.2,18.16,18.16,0,0,1-5.121,25.173c-.206.14-.412.28-.618.412a.283.283,0,0,0-.092.353.15.15,0,0,0,.066.066.292.292,0,0,0,.36-.012,17.784,17.784,0,0,1,15.412,1.983,18.509,18.509,0,0,1,5.427,5.38,18.171,18.171,0,0,1-5.112,25.18Zm0,0" transform="translate(-70.353 0)" fill="#fdd7bd"/>
                          <path id="Tracé_393541" data-name="Tracé 393541" d="M170.544,328.1l-33.789,22.376-48.781,32.3a140.409,140.409,0,0,1-85.3-110.354l56.61-37.489,34.461-22.823a8.622,8.622,0,0,1,11.951,2.438l67.284,101.591a8.631,8.631,0,0,1-2.438,11.958Zm0,0" transform="translate(-0.856 -82.92)" fill="#87dbff"/>
                          <path id="Tracé_393542" data-name="Tracé 393542" d="M207.286,328.1,173.5,350.476,96.027,234.937l34.461-22.824a8.622,8.622,0,0,1,11.951,2.438L209.723,316.14a8.633,8.633,0,0,1-2.438,11.96Zm0,0" transform="translate(-37.598 -82.92)" fill="#6fc7ff"/>
                          <g id="Groupe_111011" data-name="Groupe 111011" transform="translate(229.849 59.827)">
                            <path id="Tracé_393543" data-name="Tracé 393543" d="M416.365,101.57l-26.651,14.263a8.633,8.633,0,0,1-11.01-2.478L399.715,99.76a.2.2,0,0,0,.258.1,18.077,18.077,0,0,1,6.493-1.2,18.279,18.279,0,0,1,9.9,2.914Zm0,0" transform="translate(-378.703 -98.656)" fill="#fac5aa"/>
                            <path id="Tracé_393544" data-name="Tracé 393544" d="M442.134,156.584l-26.071,13.94a8.625,8.625,0,0,1-11.013-2.468l21.011-13.6.907-.426a.386.386,0,0,0,.512.1,18.643,18.643,0,0,1,4.408-.521,19.123,19.123,0,0,1,10.245,2.97Zm0,0" transform="translate(-389.073 -120.287)" fill="#fac5aa"/>
                            <path id="Tracé_393545" data-name="Tracé 393545" d="M469.394,211.327,446.43,223.611a8.642,8.642,0,0,1-11.013-2.478l18.2-11.778a.29.29,0,0,0,.36-.014,17.8,17.8,0,0,1,15.414,1.985Zm0,0" transform="translate(-401.025 -141.854)" fill="#fac5aa"/>
                          </g>
                        </g>
                      </svg>
                      
                </div>
                <div style="position: relative;top: 63px; right:44px;" class="svg3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="400" height="100" viewBox="0 0 144 144">
                        <circle id="Ellipse_4844" data-name="Ellipse 4844" cx="72" cy="72" r="72" fill="#ff92de"/>
                      </svg>
                      
                </div>
            </div>    
            <div style="display:flex;flex-direction:row; justify-content: space-evenly; font-family: Helvetica;" class="information">
                <div  style="display: flex; flex-direction: column;position: relative; top: -47px;" class="text">
                    <div style="display: flex; flex-direction: row; align-items: center;" class="display">
                        <span style="font-size: 32px;line-height: 17px;color: #232323;">Voici votre code de confirmation </span>  
                        <div style="position: relative; left: 112px;" class="bubble">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 106 106">
                                <circle id="Ellipse_4845" data-name="Ellipse 4845" cx="53" cy="53" r="53" fill="#50b8ff"/>
                              </svg>
                        </div>
                                                 
                    </div>
                    <h1 style="letter-spacing: 13px; font-size: 36px;line-height: 43px;color: #232323; text-align: center;">'.$code.'</h1>

                </div>
            </div>  

            <div style="display: flex; flex-direction: row; justify-content: space-evenly; position: relative; top: -10px;" class="final">
                <div style="position: relative;left: 262px;" class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="179" height="55" viewBox="0 0 179 55">
                        <defs>
                          <pattern id="pattern" preserveAspectRatio="none" width="100%" height="100%" viewBox="0 0 429 129">
                            <image width="200" height="200" xlink:href="data:image/png;base64,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"/>
                          </pattern>
                        </defs>
                        <rect id="UI-WAZA" width="179" height="55" fill="url(#pattern)"/>
                      </svg>                      
                </div>
                <div style="position: relative; top: -184px; left: 276px;" class="svg4">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="300" height="300" viewBox="0 0 782.006 927.092">
                        <defs>
                          <filter id="Tracé_393537" x="0" y="0" width="782.006" height="927.092" filterUnits="userSpaceOnUse">
                            <feOffset dy="3" input="SourceAlpha"/>
                            <feGaussianBlur stdDeviation="3" result="blur"/>
                            <feFlood flood-opacity="0.161"/>
                            <feComposite operator="in" in2="blur"/>
                            <feComposite in="SourceGraphic"/>
                          </filter>
                        </defs>
                        <g transform="matrix(1, 0, 0, 1, 0, 0)" filter="url(#Tracé_393537)">
                          <path id="Tracé_393537-2" data-name="Tracé 393537" d="M-16588-17472.727s-84.727-424.24,366.788-490.908,375.758-272.729,375.758-272.729l15.151,909.092Z" transform="translate(16603.31 18242.36)" fill="#50b8ff"/>
                        </g>
                      </svg>
                      
                </div>
            </div>
        </div>
    </div>';
    return $message;
    }
}
