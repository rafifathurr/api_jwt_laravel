<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::namespace('App\Http\Controllers')->group(function (){

    Route::prefix('auth')->group(function (){
        Route::post('/signup', 'UserController@register');
        Route::post('/login', 'UserController@login');
    });

    Route::prefix('user')->group(function(){
        Route::get('/userlist', 'UserController@userlist');
        Route::post('/logout', 'UserController@logout');
    });
});
