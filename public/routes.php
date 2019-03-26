<?php

Route::get('/', function(){
	return View::make('users.index');
});

Route::get('login',function(){
	return View::make('users.login');
});

Route::get('google-signin',function(){
	$storage = new Session();
	$code = Input::get( 'code' );
    $googleService = OAuth::consumer( 'Google' );

    if (!empty($code)) {
    // This was a callback request from google, get the token
    $token = $googleService->requestAccessToken($code);
    Session::put("gtoken",$token->getAccessToken());
    // Send a request with it
    $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
    $files = json_decode($googleService->request('https://www.googleapis.com/drive/v2/files'), true);
    $quota = json_decode($googleService->request('https://www.googleapis.com/drive/v2/about'), true);
    Session::put("files",$files);
    Session::put("quota",$quota);
    // Show some of the resultant data
    echo 'Your unique google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
    return Redirect::to('google_getstorage');

	} 
	elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	    $url = $googleService->getAuthorizationUri();
	  	return Redirect::to( (string)$url );
	  	//echo $url;
	} 
	else {
	   	$url = Request::url() . '?go=go';
	    return Redirect::to($url);
	}
});


Route::get('dropbox-signin',function(){
	$storage = new Session();
	$code = Input::get( 'code' );
    $dropboxService = OAuth::consumer( 'Dropbox' );


	if (!empty($code)) {
	    // This was a callback request from Dropbox, get the token
	    $token = $dropboxService->requestAccessToken($code);
	    Session::put("dtoken",$token->getAccessToken());
	    // Send a request with it
	    $result = json_decode($dropboxService->request('/account/info'), true);
	    // Show some of the resultant data
	    echo 'Your unique Dropbox user id is: ' . $result['uid'] . ' and your name is ' . $result['display_name'];

	    /*echo '<pre>';
		dd($result);
		echo '</pre>';*/

		return Redirect::to('dropbox_getfile/index');
	} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
 		//goes to Dropbox to sign in
	    $url = $dropboxService->getAuthorizationUri();
	    return Redirect::to( (string)$url );
	} else {
		//Request::url() is current url
	    $url = Request::url() . '?go=go';
	    return Redirect::to($url);
	}
});

Route::get('logout',function(){

Session::flush();
return Redirect::to('/');
});

Route::get('dropbox_getfile/{path}','GetFileController@getFile');
Route::get('google_getfile','GoogleApiController@getFile');
Route::get('google_uploadfile','GoogleApiController@uploadFile');
Route::get('google_getstorage','GoogleApiController@getQuota');

