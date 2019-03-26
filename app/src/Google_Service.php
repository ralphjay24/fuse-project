<?php



class Google_Service{
    
  public function __construct( Google_Client $client )
  {
    $this->client = $client;
    $this->init();
	}
 
	private function init()
	{
      $this->client->setClientId(Config::get('google.client_id') );
      $this->client->setClientSecret(Config::get('google.client_secret'));
      $this->client->setDeveloperKey(Config::get('google.api_key'));
      $this->client->setRedirectUri('http://localhost:8000/google_login');
      $this->client->setScopes(array('https://www.googleapis.com/auth/drive'));
      $this->client->setAccessType('offline');
      $this->client->setPrompt('select_account');
      /*if(Session::has('user'))
        if(strtoupper(substr(strrchr(Session::get('user')->email, "@"), 1)) == 'gmail.com')
          $this->client->setLoginHint(Session::get('user')->email);*/
      
	}

	public function getRequest($r)
	{
  		$request = new Google_Auth_OAuth2($this->client);
  		return $request->authenticatedRequest($r);
	}

	public function getService()
	{
  		return new Google_Service_Drive($this->client);
	}

	public function isLoggedIn()
  {
    if(Session::has('gtoken'))
    {
      $gtoken = json_decode(Session::get('gtoken')['token'],true);
      $expiration = ((time()+240)-$gtoken['created'])+1800 ;
      
      if (!empty(Session::get('gtoken')['token']) && $expiration <= $gtoken['expires_in']) 
      {
          $this->client->setAccessToken(Session::get('gtoken')['token']);
          
      }
      else
      {
          $refresh = Session::get('gtoken')['refresh'];
          $gtoken = $this->refreshToken($refresh);
          Session::put('gtoken',['token' => $gtoken, 'refresh' => $refresh]);
      }

 
      return true;
    }
    else
      return false;
  }

  public function refreshToken($token)
  {
      $this->client->refreshToken($token);
      return $this->client->getAccessToken();
  }
 
	public function login( $code )//login
	{     
		  $this->client->authenticate($code);
    	$token = $this->client->getAccessToken();
    	
             
    	return $token;
	}
 
	public function getLoginUrl()//getLoginUrl
	{
    	$authUrl = $this->client->createAuthUrl();
    	return $authUrl;
	}

  public function accountInfo()
  {
    if($this->isLoggedIn())
    {
      $service = $this->getService();
      try {
          $about = $service->about->get();

          return $about;
      } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
      }
    }
  }

  public function quotaInfo()
  {
    if($this->isLoggedIn())
    {
      $quota = array();
      $google_account = $this->accountInfo();
      
      $quota['total'] = $google_account->getQuotaBytesTotal()-2147483648;
      $quota['used'] = $google_account->getQuotaBytesUsed();

      return $quota;
    }
  }

	public function getFile($fileId) 
	{
  		$service = $this->getService();
    	try {
      		$file = $service->files->get($fileId);
      		return $file;
  		} catch (Exception $e) {
      		echo "An error occurred: " . $e->getMessage();
    	}
	}

  public function resumableUpload($f,$parent_id)
  {
      $service = $this->getService();
      $chunkSizeBytes = 104857600;

      $file = new Google_Service_Drive_DriveFile();
      
      $file->setTitle($f->getClientOriginalName());
      $file->setMimeType($f->getClientMimeType());

      
      if ($parent_id != null) 
      {
        $parent = new Google_Service_Drive_ParentReference();
        $parent->setId($parent_id);
        $file->setParents(array($parent));
      }
      
      $this->client->setDefer(true);
      $data = file_get_contents($f->getRealPath());

      $createdFile = $service->files->insert($file, array(
            'data' => $data,
            'uploadType' => 'resumable'
          ));
      // Create a media file upload to represent our upload process.
      $media = new Google_Http_MediaFileUpload(
      $this->client,
      $createdFile,
      $f->getClientMimeType(),
      $data,
      true,
      $chunkSizeBytes
      );
      $media->setFileSize(filesize($f->getRealPath()));

      // Upload the various chunks. $status will be false until the process is
      // complete.
      $status = false;
      $handle = fopen($f->getRealPath(), "rb");
      while (!$status && !feof($handle)) {
        $chunk = fread($handle, $chunkSizeBytes);
        $status = $media->nextChunk($chunk);
      }

      // The final value of $status will be the data from the API for the object
      // that has been uploaded.
      $result = false;
      if($status != false) {
        $result = $status;
      }

      fclose($handle);
      // Reset to the client to execute requests immediately in the future.
      $this->client->setDefer(false);
      return $result;
  }

	public function createFolder($root,$folder_name) 
	{
		
  		$service = $this->getService();
    	$file = new Google_Service_Drive_DriveFile();
    	$file->setTitle($folder_name);
  		
  		$file->setMimeType('application/vnd.google-apps.folder');


  		// Set the parent folder.
  		if ($root != null) 
  		{
    		$parent = new Google_Service_Drive_ParentReference();
    		$parent->setId($root);
    		$file->setParents(array($parent));
  		}
		  
    		try {
      		

  			$createdFile = $service->files->insert($file, array(
        		'uploadType' => 'multipart'
      		));

      	
        return $createdFile;
      		
    		} catch (Exception $e) {
      		print "An error occurred: " . $e->getMessage();
    		}
  	  
	}

	public function searchFile($search)
	{
		  $service = $this->getService();
  		$result = array();
  		$pageToken = NULL;

  		do {
    		try {
      			$parameters = array();
      			if ($pageToken) {
        			$parameters['pageToken'] = $pageToken;
      			}
      			
      			$parameters['q'] = "title='".$search."'";
      			$files = $service->files->listFiles($parameters);

      			$result = array_merge($result, $files->getItems());
      			$pageToken = $files->getNextPageToken();

      			return $result;
    		} catch (Exception $e) {
      			print "An error occurred: " . $e->getMessage();
      			$pageToken = NULL;
    		}
  		} while ($pageToken);
  		
	}


	public function allFiles($folder_path) 
	{
		  
  		
      $content = array('items'=>array());
  		

      if($folder_path === "My_Drive")
  		  $id = $this->searchFile('Fuse Storage');
      else
        $id = $this->searchFile($folder_path);

  		if(!empty($id))
      {

        $file = $this->_getFiles($id);
        
        if(!empty($file))
        {
            foreach ($file as $files) 
            {
                $print['file_id'] = $files['id'];
                
                $print['file_name'] = $files['title'];
              
                if(!empty($files['fileSize']))
                    $print['file_size'] = $this->formatBytes($files['fileSize']);
                else
                    $print['file_size'] = ' ';
                
                $print['file_modified'] = date("F j, Y, g:i a",strtotime($files['modifiedDate'])+28800);
                
            
                if($files['mimeType'] == 'application/vnd.google-apps.folder')
                {
                    $print['file_type'] = 'Folder';
                }
                else
                {   
                    $print['file_type'] = $this->fileType($files['title']).' File';
                }
                
                $parentd = $files['parents'][0]['id'];
                $print['location'] = 'Google';
                array_push($content['items'], $print);
            }
            
            
        		
        }
        if(empty($parentd))
            $content['parent_id'] = $id[0]['id'];
        else
            $content['parent_id'] = $parentd;
      }
      return $content;
      
	}

  private function _getFiles($id)
  {
      $service = $this->getService();
      $pageToken = NULL;
      $result = array();
      do 
        {
          
          try 
          {
              $parameters = array();
              
              if ($pageToken) 
                $parameters['pageToken'] = $pageToken;
              
              
              $parameters['q'] = "'".$id[0]['id']."' in parents";

              $files = $service->files->listFiles($parameters);

              $file = array_merge($result, $files->getItems());
              $pageToken = $files->getNextPageToken();
              
            
          } 
          catch (Exception $e) 
          {
              echo "An error occurred: " . $e->getMessage();
              $pageToken = NULL;
          }

      } while ($pageToken);
      return $file;
  }

  private function fileType($type)
  {
      return strtoupper(substr(strrchr($type, "."), 1));
  }

	public function formatBytes($bytes, $precision = 2) 
	{ 
    	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    	$bytes = max($bytes, 0); 
    	$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    	$pow = min($pow, count($units) - 1); 

    	// Uncomment one of the following alternatives
    	$bytes /= pow(1024, $pow);
    	// $bytes /= (1 << (10 * $pow)); 

    	return round($bytes, $precision) . ' ' . $units[$pow]; 
	}

  

  public function downloadFile($fileId)
  {
    if( $this->isLoggedIn() )
    {
        //$service = $this->gd->getService();
        $file = $this->getFile($fileId);
        if(!empty($file['downloadUrl']))
          $url = $file['downloadUrl'];
        else
          $url = $file['exportLinks'][$file['mimeType']];
        
        //return $file;

        if ($url) {
          $request = new Google_Http_Request($url, 'GET', null, null);
            
          $signhttpRequest = $this->client->getAuth()->sign($request);
          $httpRequest = $this->client->getIo()->makeRequest($signhttpRequest);
          if ($httpRequest->getResponseHttpCode() == 200){ 
              $content = $httpRequest->getResponseBody();
              
              $f = fopen('Downloads/'.Session::get('user')->user_id.'/'.$file['title'], 'wb');
              fwrite($f, $content);
              fclose($f);
              
          } else {

            return 'File download failed.';
          }
        } else {
          // The file doesn't have any content stored on Drive.
          return "<html>File content is empty.</html>";
        }
    }
  }

  public function deleteFile($fileId)
  {
    if( $this->isLoggedIn() )
    {
      $service = $this->getService();
      try 
      {
        $service->files->delete($fileId);
      }
      catch (Exception $e) 
      {
        print "An error occurred: " . $e->getMessage();
      }
    }
  } 
}



?>