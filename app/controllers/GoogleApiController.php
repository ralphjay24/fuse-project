<?php

class GoogleApiController extends BaseController {
    private $gd;
    private $gservice;
 
    public function __construct(Google_Service $gd)
    {
        $this->gd = $gd;
        $this->gservice = $this->gd->getService();
    }
 
    public function index()//index
    {
      if(Session::has('user') || Session::has('register'))
        return Redirect::to($this->gd->getLoginUrl());
      else
        return Redirect::to('login')->with('global','Need to login');
    }

    public function login()//login
    {
    	if( Input::has('code') )
    	{
        	$code = Input::get('code');
        	$token = json_decode($this->gd->login($code),true);
        	
          if(Session::has('register'))
        	  $current = Session::get('register');

          if(Session::has('user'))
            $current = Session::get('user');

          $search = Token::find($current->user_id);
          

        	if(empty($search))
        	{
        		DB::table('token')
        		->insert(array(
   					 	'user_id' => $current->user_id,
   					 	'gtoken'  => $token['refresh_token']
				    ));
        	}
        	elseif(array_key_exists('refresh_token', $token))
        	{
        		DB::table('token')
    				->where('user_id', $current->user_id)
    				->update(['gtoken' => $token['refresh_token']
            ]);
    			}

          DB::table('users')
          ->where('user_id', $current->user_id)
          ->update(['isGoogle' => 'accepted']);
			
        	
          if(Session::has('register'))
          {
            if(Session::has('storage'))
            {
              $storage = Session::get('storage');
              if(!in_array('google', $storage))
              {
                array_push($storage, 'google');
                Session::put('storage',$storage);  
              }
              
            }
            else
            {
                Session::put('storage',array('google'));
                  
            }
            return Redirect::to('auth_register');
          }
          if(Session::has('user'))  
        	   return Redirect::to('process');
    	}
    	else{
        	return Redirect::to('google');
    	}
	}

	public function insertFile($root) 
	{
		$f = Input::file('file');

		if($this->gd->isLoggedIn())
    	{
  			try {
    		

    	  		$createdFile = $this->gd->resumableUpload($f,$root);
        
    			//return Redirect::to('/display');
  			} catch (Exception $e) {
    			print "An error occurred: " . $e->getMessage();
  			}
  		}
	}
	public function createFolder($root) 
	{
		
		$this->gd->createFolder($root,'New Folder');

		return Redirect::to('/display');
  		
	}

	public function displayAll()
	{
		if( $this->gd->isLoggedIn() )
		{
			$file = $this->gd->allFiles();
			
			echo "<pre>";
		  	print_r($file);
		  	echo "</pre>";
		  	//return View::make('upload')->with(['files' => $content]);
		}
	}

	public function deleteFile($fileId) 
	{
		if( $this->gd->isLoggedIn() )
		{
			//$service = $this->gservice;
  			try {
    			$this->gservice->files->delete($fileId);
    			return Redirect::to('/display');
  			} catch (Exception $e) {
    			echo "An error occurred: " . $e->getMessage();
  			}
  		}
	}

	public function downloadFile($fileId) {
		if( $this->gd->isLoggedIn() )
		{
			//$service = $this->gd->getService();
			$file = $this->gd->getFile($fileId);
			if(!empty($file['downloadUrl']))
				$url = $file['webContentLink'];
			elseif($file['mimeType'] == 'application/vnd.google-apps.document')
				$url = $file['exportLinks'][$file['mimeType']];
			elseif($file['mimeType'] == 'application/vnd.google-apps.presentation')
				$url = $file['exportLinks'][$file['mimeType']];
			elseif($file['mimeType'] == 'application/vnd.google-apps.spreadsheet')
				$url = $file['exportLinks'][$file['mimeType']];
			else
				$url = false;
			
			if ($url) {
    			$request = new Google_Http_Request($url, 'GET', null, null);
    			$httpRequest = $this->gd->getRequest($request);
    			if ($httpRequest->getResponseHttpCode() == 200) {
      				return $httpRequest->getResponseBody();
    			} else {
      				// An error occurred.
      			print_r($request);
    			}
  			} else {
    			// The file doesn't have any content stored on Drive.
    			return "<html>Failed</html>";
  			}
  		}	
	}


}
