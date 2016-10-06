<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::post('/message','Api\ApiHomeController@sendMessage')->name('sendMessage')->middleware('auth');
Route::post('/setMessage','Api\ApiHomeController@setMessage')->name('setMessage')->middleware('auth');
Route::get('/offLineMessage','Api\ApiHomeController@offLineMessage')->name('offLineMessage')->middleware('auth');
Route::get('/getFriendList','Api\ApiHomeController@getFriendList')->name('getFriendList')->middleware('auth');
Route::get('/send','HomeController@send')->name('send')->middleware('auth');