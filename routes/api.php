<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use Illuminate\Support\Facades\Hash;
// use Mail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('signup', [AuthController::class, 'signup']);

Route::post('login', [AuthController::class , 'login']);
Route::post('logout', [AuthController::class , 'logout'])->middleware('auth:sanctum');

Route::post('forgot-password', [ForgotPasswordController::class , 'submitForgotPasswordForm'])->middleware('auth:sanctum');

Route::post('reset-password', [ForgotPasswordController::class , 'submitResetPasswordForm'])->middleware('auth:sanctum');

Route::post('resend-otp', [ForgotPasswordController::class , 'resendOTP'])->middleware('auth:sanctum');
