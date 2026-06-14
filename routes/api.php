<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/information', function(){
    $user = User::all();
    return response()->json([
        'success' => true,
        'message' => 'Todo esta corriendo bien',
        'users' => $user
    ]);
});
