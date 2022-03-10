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

Route::get('/php-to-text','PhpToTextController@phpToText');
Route::get('/route','RouteController@route');
Route::get('/test','RouteController@test');
Route::post('/json-to-markdown','JsonToMarkdownController@jsonToMarkDown');
Route::get('/field','FieldController@field');
Route::get('/sidebar','SidebarController@sidebar');

