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

use Illuminate\Http\Request;
use App\User;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'api'], function(){

    //Get all users JSON Only
    Route::get('getAllUsers', function (){
        return response()->json(User::all(), 200);
    });
    //Get user By email
    Route::post('getUserByEmail', 'UserController@getUserByEmail');

    //Create a new User
    Route::post('createUser', 'UserController@createUser');

    //Delete a new User
    Route::post('deleteUser', 'UserController@deleteUser');

    //Update User Password
    Route::post('updateUserPassword', 'UserController@updateUserPassword');

    //Update User Email
    Route::post('updateUserEmail', 'UserController@updateUserEmail');

    //Update User Name
    Route::post('updateUserName', 'UserController@updateUserName');


});
