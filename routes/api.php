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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'as' => 'api.'], function (){
    $exceptCreateEdit = ['except' => ['create', 'edit']];

    Route::resource('categories', 'CategoryController', $exceptCreateEdit);
    Route::resource('genres', 'GenreController', $exceptCreateEdit);
    Route::resource('cast_members', 'CastMemberController', $exceptCreateEdit);
    Route::resource('videos', 'VideoController', $exceptCreateEdit);
});
