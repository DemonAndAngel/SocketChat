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
Route::get('/', 'HomeController@index')->middleware('auth');
Route::get('/home', 'HomeController@index')->middleware('auth');
Route::post('/message','Api\ApiHomeController@sendMessage')->name('sendMessage')->middleware('auth');
Route::post('/setMessage','Api\ApiHomeController@setMessage')->name('setMessage')->middleware('auth');
Route::post('/setMessageForId','Api\ApiHomeController@setMessageForId')->name('setMessageForId')->middleware('auth');
//Route::get('/offLineMessage','Api\ApiHomeController@offLineMessage')->name('offLineMessage')->middleware('auth');
Route::get('/getMessageList','Api\ApiHomeController@getMessageList')->name('getMessageList')->middleware('auth');
Route::get('/getMessageListInfo','Api\ApiHomeController@getMessageListInfo')->name('getMessageListInfo')->middleware('auth');
Route::get('/getFriendList','Api\ApiHomeController@getFriendList')->name('getFriendList')->middleware('auth');
Route::get('/chatPage','HomeController@chatPage')->name('chatPage')->middleware('auth');