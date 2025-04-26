<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;


class ForgotPasswordController extends Controller
{

    // submit forget password form
    public function submitForgotPasswordForm(Request $request){

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => "required|email|exists:users,email",
                ]
            );

            if($validateUser->fails()){
                return response()->json(
                [
                    'status' => false,
                    'message' => "Authentication Error",
                    'error' => $validateUser->errors()->first(),
                ],404);
            }


           $otp = mt_rand(100000, 999999);

            $createToken = DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' =>$otp,
                'created_at' => Carbon::now(),
            ]);


            Mail::raw("Your password reset OTP is: $otp", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Reset Password');
            });


            return response()->json([
                'status' => true,
                'message' =>  'Your OTP has been sent to your registered email address.',
                 'OTP' => $otp,
            ],201);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => "something went's Wrong. Please try again",
                'errors' => $e->getMessage(),
            ], 500);
        }

    }


    // resend OTP
    public function resendOTP(Request $request){

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => "required|email|exists:users,email",
                ]
            );

            if($validateUser->fails()){
                return response()->json(
                [
                    'status' => false,
                    'message' => "Authentication Error",
                    'error' => $validateUser->errors()->first(),
                ],404);
            }


           $otp = mt_rand(100000, 999999);

            $createToken = DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' =>$otp,
                'created_at' => Carbon::now(),
            ]);


            Mail::raw("Your password reset OTP is: $otp", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Reset Password');
            });


            return response()->json([
                'status' => true,
                'message' =>  'Your OTP has been sent to your registered email address.',
                 'OTP' => $otp,
            ],201);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => "something went's Wrong. Please try again",
                'errors' => $e->getMessage(),
            ], 500);
        }

    }



    // submit reset password form with token


    public function submitResetPasswordForm(Request $request){

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => "required|email|exists:users,email",
                    'password' => 'required|string|min:6|confirmed',
                    'password_confirmation' => 'required',
                    'otp' => 'required'
                ]
            );

            if($validateUser->fails()){
                return response()->json(
                [
                    'status' => false,
                    'message' => "Authentication Error",
                    'error' => $validateUser->errors()->first(),
                ],404);
            }


            $otpExists = DB::table('password_resets')
            ->where(
                [
                    'email' => $request->email,
                    'token' => $request->otp
                ]
            )->first();

            if(!$otpExists){
                return response()->json(
                    [
                        'status' => false,
                        'message' => "Please Enter a Valid Otp. ",
                    ],404);
            }

            $otpCreatedAt  = Carbon::parse($otpExists->created_at);
            // return $otpCreatedAt ;

            if($otpCreatedAt->diffInMinutes(Carbon::now()) > 1){
                return response()->json(
                    [
                        'status' => false,
                        'message' => "Your Otp has been Expired. Please Request a new one.",
                    ],404);
            }


            // update password

                User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

                $user = User::where('email', $request->email)->get();


                // delete otp Email from reset password table

              DB::table('password_resets')->where(['email'=> $request->email])->delete();

              return response()->json([
                'status' => true,
                'message' =>  'Your Password Has been Changed',
                 'user' => $user,
             ],201);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => "something went's Wrong. Please try again",
                'errors' => $e->getMessage(),
            ], 500);
        }



    }
}


