<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('users', 'User\UserController', ['except' => ['create']]);
Route::resource('categories', 'Category\CategoryController', ['except' => ['create']]);
Route::name('verify')->get('users/verify/{token}', 'User\UserController@verify');
Route::name('resend')->get('users/{user}/resend', 'User\UserController@resend');

//Route::get('prova', function () {
//    Log::info('A user has arrived at the welcome page.');
//    return view('welcome');
//});
