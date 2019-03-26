<?php

Route::get('/', function(){
	if(Session::has('user'))
 		return Redirect::to('home/My_Drive');
 	else
 		return View::make('home');
});

Route::get('/home/{folder_path}', 'MainController@display');
Route::get('process', 'MainController@index');
Route::get('add/{storage}', 'MainController@addStorage');
Route::post('/upload', 'MainController@uploadFile');
Route::post('createFolder', 'MainController@createFolder');
Route::post('download', 'MainController@download');
Route::post('delete', 'MainController@delete');
Route::post('evaluate', 'MainController@evaluateAction');

#Google
Route::get('/google', 'GoogleApiController@index');
Route::get('/google_login', 'GoogleApiController@login');

Route::get('/display', 'GoogleApiController@displayAll');

Route::get('/logout',function(){
	DB::table('file')->truncate();
	Session::flush();
	return Redirect::to('login');
});

#Dropbox
Route::get('/dropbox', 'DropboxApiController@index');
Route::get('/dropbox_login', 'DropboxApiController@login');
Route::get('/dropbox_display', 'DropboxApiController@displayFiles');
Route::post('/dropbox_upload', 'DropboxApiController@uploadFile');

#Box
Route::get('box-signin','BoxApiController@login');
Route::get('box','BoxApiController@index');
Route::get('box_display','BoxApiController@display'); //files rani gipass


//User Registration
Route::get('register','UsersController@getRegister');
Route::get('auth_register','UsersController@getAuthenticate');
Route::get('authenticate/{$storage}', 'UsersController@doAuthenticate');
Route::get('success', 'UsersController@success_register');
Route::get('login', 'UsersController@getLogin');
Route::post('register','UsersController@doRegister');
Route::post('login','UsersController@doLogin');
Route::get('/logout','UsersController@logout');
