<?php

namespace App\Http\Controllers;
use App\Helper\JWTToken;
use App\Helper\ResponseHelper;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function LoginPage()
    {
        return view('pages.login-page');
    }

    public function VerifyPage()
    {
        return view('pages.verify-page');
    }




                              // user Authentication section
    public function UserLogin(Request $request):JsonResponse
    {
        try {
            $UserEmail=$request->UserEmail;      // taking user email into a variable
            $OTP=rand (100000,999999);  //genarate 6 digit otp code
            $details = ['code' => $OTP];
               //sending otp code to that mail address
            Mail::to($UserEmail)->send(new OTPMail($details));
            User::updateOrCreate(['email' => $UserEmail], ['email'=>$UserEmail,'otp'=>$OTP]);
            return ResponseHelper::Out('success',"A 6 Digit OTP has been send to your email address",200);
        } catch (Exception $e) {
            return ResponseHelper::Out('fail',$e,200);
        }
    }


    public function VerifyLogin(Request $request):JsonResponse
    {
            $UserEmail=$request->UserEmail;
            $OTP=$request->OTP;

        $verification = User::where('email', $UserEmail)->where('otp', $OTP)->first();

        if ($verification) {
            User::where('email', $UserEmail)->where('otp', $OTP)->update(['otp' => '0']);
            $token = JWTToken::CreateToken($UserEmail, $verification->id);
            return  ResponseHelper::Out('success', "", 200)->cookie('token', $token, 60 * 24 * 30);
        } else {
            return  ResponseHelper::Out('fail', null, 401);
        }
    }

    function UserLogout()
    {
        return redirect('/UserLogin/{UserEmail}')->cookie('token', '', -1);
    }
}
