<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::get();
        return $user;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function signup(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|min:6|max:20',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6|max:10',
                ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => "Validation Error",
                    'errors' => $validateUser->errors()->first(),
                ],401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $registerToken = $user->createToken("Register Token")->plainTextToken;

            return response()->json([
               'status' => true,
               'message' => "User Created SuccessFully",
               'token' => $registerToken,
               'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Something wents wrong, Please try again.",
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function login(Request $request)
    {

        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => "Authenticatin Failed",
                    'errors' => $validateUser->errors()->first(),
                ],404);
            }

            // if the user is valid

            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $authUser = Auth::user();

                return response()->json([
                    'status' => true,
                    'message' => "User Logged In Successfully",
                    'token' => $authUser->createToken("Login TOKEN")->plainTextToken,
                    'user' => $authUser,
                ], 201);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => "Invalid Credentials! Email or Password doesn't match. ",
                ], 401);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Something wents wrong, Please try again.",
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();


            return response()->json([
                'status' => true,
                'message' => "You're Logged Out",
                'user' => $user,
            ], 201);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => "something wents wrong. Please try again",
                'error' => $e->getMessage(),
            ],500);

        }


    }


}
