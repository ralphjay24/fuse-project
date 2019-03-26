<?php


use \Dropbox as dbx;

class Dropbox_Service {

	private $app;

	public function __construct()
    {
    	$this->getWebAuth();
    	
	}


	private function getWebAuth()
	{
   		$appInfo = new dbx\AppInfo(Config::get('dropbox.app_key'), Config::get('dropbox.app_secret'));
   		$clientIdentifier = Config::get('dropbox.app_name');
   		$redirectUri = "http://localhost:8000/dropbox_login";
   		$csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
   		
   		

   		return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore,null);
	}

	public function getAuthUrl()
	{
		return $this->getWebAuth()->start();
	}

	public function login($get)
	{
		
		try 
		{
   			list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($get);
   			//assert($urlState === null);  // Since we didn't pass anything in start()
   			
   			return array('token' => $accessToken, 'userid' => $userId);
		}
		catch (dbx\WebAuthException_BadRequest $ex) {
   			echo "/dropbox-auth-finish: bad request: " . $ex->getMessage();
   			// Respond with an HTTP 400 and display error page...
		}
		catch (dbx\WebAuthException_BadState $ex) {
   			// Auth session expired.  Restart the auth process.
   			Redirect::to('dropbox');
		}
		catch (dbx\WebAuthException_Csrf $ex) {
   			echo "/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage();
   			// Respond with HTTP 403 and display error page...
		}
		catch (dbx\WebAuthException_NotApproved $ex) {
   			echo "/dropbox-auth-finish: not approved: " . $ex->getMessage();
		}
		catch (dbx\WebAuthException_Provider $ex) {
   			echo "/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage();
		}
		catch (dbx\Exception $ex) {
   			echo "/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage();
		}

		
	}

	public function accountInfo($token)
	{
		try{
			$client = new dbx\Client($token, Config::get('dropbox.app_name'));
			return $client->getAccountInfo();
		}catch(dbx\Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function quotaInfo($token)
  	{
      	$quota = array();
      	$dropbox_account = $this->accountInfo($token);
      
      	$quota['total'] = $dropbox_account['quota_info']['quota'];//-2147483648;
      	$quota['used'] = $dropbox_account['quota_info']['normal'];

      	return $quota;
  	}

	public function search($token, $string)
	{
		try{
			$client = new dbx\Client($token, Config::get('dropbox.app_name'));
			return $client->searchFileNames('/',$string);
		}catch(dbx\Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function createFolder($token, $string)
	{
		try{
			$client = new dbx\Client($token, Config::get('dropbox.app_name'));
			return $client->createFolder($string);
		}catch(dbx\Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function allFiles($token, $folder_path)
	{
		$content = array('items'=>array());
		try{
			$client = new dbx\Client($token, Config::get('dropbox.app_name'));
			$path = new dbx\Path();
			
			if($folder_path === 'My_Drive')
				$root = $this->search($token, 'Fuse Storage');
			else
				$root = $this->search($token, $folder_path);
			
			if(!empty($root))
			{
				$files = $client->getMetadataWithChildren($root[0]['path']);
			

				
				foreach ($files['contents'] as $file) 
				{	
					$name = $path->getName($file['path']);
					$print['file_name'] = $name;
					$print['file_id'] = $file['path'];
					
					$print['root'] = $file['root'];

					
					$print['file_modified'] = $client->parseDateTime($file['modified'])->format('F j, Y, g:i a');
					$modified = date("F j, Y, g:i a",strtotime($print['file_modified'])+28800);
					$print['file_modified'] = $modified;
					
						if($file['is_dir'] == 1){
							$print['file_type'] = 'Folder';
							$print['file_path'] = $file['path'];
							$print['file_size'] = ' ';
							
						}else{
							$print['file_type'] = $this->fileType($name);//$file['mime_type'];
							$print['file_size'] = $file['size'];
							
						}
				 	$print['location'] = 'Dropbox';
					array_push($content['items'],$print);
					

				}
				$content['parent_id'] = $root[0]['path'];
			}

			return $content;
		}catch(dbx\Exception $e)
		{
			echo $e->getMessage();
		}
	}

	private function fileType($type)
  	{
  		if(strrchr($type, "."))
    		return strtoupper(substr(strrchr($type, "."), 1)).' File';
    	else
    		return 'File';
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

	public function resumableUpload($token,$file,$parent_path)
	{
			
			
		  	$filename = $file->getClientOriginalName();
		  	$destinationPath = $file->getRealPath();		

			$client = new dbx\Client($token,Config::get('dropbox.app_name'));
			$f = fopen($file->getRealPath(), "rb");
			$s = filesize($file->getRealPath());
			$result = $client->uploadFileChunked($parent_path.'/'.$filename, dbx\WriteMode::add(), $f, $s);
		  	fclose($f);

		  	return $result;
	}

	public function downloadFile($token,$fileId,$name)
	{
		

		try{
			$client = new dbx\Client($token, Config::get('dropbox.app_name'));
			
			$f = fopen('Downloads/'.Session::get('user')->user_id.'/'.$name, "wb");
			$fpath = str_replace(array('%20','%2F','%5C', '%252F','%255C'), '/', $fileId);
			$metadata = $client->getFile($fpath,$f);
			fclose($f);
			

		}catch(dbx\Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function deleteFile($token, $filePath)
	{
		$client = new dbx\Client($token,Config::get('dropbox.app_name'));
		$delPath = $client->delete($filePath);

		return $delPath;
	}

	

	


}