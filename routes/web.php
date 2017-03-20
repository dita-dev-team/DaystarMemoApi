<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'api'], function () {

    Route::post('user/change-password', 'UserController@change')->middleware('auth:api');
    Route::get('user/profile', 'UserController@profile')->middleware('auth:api');
    Route::resource('groups', 'GroupController', ['except' => [
        'edit', 'create'
    ]]);

    Route::post('groups/{id}/join', 'GroupDetailsController@join');
    Route::post('groups/{id}/leave', 'GroupDetailsController@leave');
    Route::post('groups/{id}/owner/add', 'GroupDetailsController@addOwner');
    Route::post('groups/{id}/owner/remove', 'GroupDetailsController@removeOwner');

    Route::resource('assets', 'AssetController', ['except' => [
        'edit', 'create'
    ]]);

    Route::resource('memos', 'MemoController', ['only' => [
        'index', 'show', 'store'
    ]]);

//    Route::post('memos/{id}/getMemo', 'MemoController@getMemoById');
});


Route::get('/testauth', function () {
    return response()->json([
        'message' => 'This is just a test authentication page'
    ]);
})->middleware('auth:api');
Auth::routes();

Route::get('/home', 'HomeController@index');
