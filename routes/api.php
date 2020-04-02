<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('register', 'UserController@register'); //register
Route::post('login', 'UserController@login'); //login

Route::middleware(['jwt.verify'])->group(function(){

//Daily Scrum
Route::get('dailyscrum', 'DailyScrumController@index'); //read daily scrum
Route::get('dailyscrum{limit}/{offset}', 'DailyScrumController@getAll'); //read daily scrum
Route::post('dailyscrum', 'DailyScrumController@store'); //create daily scrum
Route::put('dailyscrum/{id}', 'DailyScrumController@update'); //update daily scrum
Route::delete('dailyscrum/{id}', 'DailyScrumController@delete'); //delete daily scrum

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
