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
    Route::resource('groups', 'GroupController', ['except' => [
        'edit', 'create'
    ]]);

    Route::post('groups/{name}/join', 'GroupDetailsController@join');
    Route::post('groups/{name}/leave', 'GroupDetailsController@leave');
    Route::post('groups/{name}/owner/add', 'GroupDetailsController@addOwner');
    Route::post('groups/{name}/owner/remove', 'GroupDetailsController@removeOwner');

    Route::resource('assets', 'AssetController', ['except' => [
        'edit', 'create'
    ]]);

    Route::resource('memos', 'MemoController', ['only' => [
        'index', 'show', 'store'
    ]]);

//    Route::post('memos/{id}/getMemo', 'MemoController@getMemoById');
});


Route::post('/store/image', 'ImageController@storePhoto');
Route::post('/update/image', 'ImageController@storePhoto');

Route::get('/retrieve/{resource}/{photo}/{id}', 'ImageController@getPhoto');
Route::get('/delete/{resource}/{id}', 'ImageController@deletePhoto');


Route::get('/testauth', function () {
    return response()->json([
        'message' => 'This is just a test authentication page'
    ]);
})->middleware('auth:api');
Auth::routes();

Route::get('/home', 'HomeController@index');
