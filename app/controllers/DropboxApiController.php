<?php

use \Dropbox as dbx;

class DropboxApiController extends BaseController{

	private $dbox;

	public function __construct(Dropbox_Service $dbx)
	{
		$this->dbox = $dbx;
	}

	public function index()
	{
		if(Session::has('user') || Session::has('register'))
			return Redirect::to($this->dbox->getAuthUrl());
		else
			return Redirect::to('login')->with('global','Need to login');
	}

	public function login()
	{
		if(!empty($_GET))
    	{
			$token = $this->dbox->login($_GET);
			
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
   					 	'dtoken'  => $token['token']
				));
        	}
        	else 
        	{
        		DB::table('token')
					->where('user_id', $current->user_id)
					->update(['dtoken' => $token['token']]);
			}

			DB::table('users')
					->where('user_id', $current->user_id)
					->update(['isDropbox' => 'accepted']);
			

			if(Session::has('register'))
          	{
            	if(Session::has('storage'))
            	{
            	  $storage = Session::get('storage');
            	  if(!in_array('dropbox', $storage))
	              {
	              	array_push($storage, 'dropbox');
	                Session::put('storage',$storage);  
	              }
            	}
            	else
            	{
            		Session::put('storage',array('dropbox'));
            	  	
            	}
            	return Redirect::to('auth_register');
          	}
          	if(Session::has('user'))  
        		return Redirect::to('process');

		}
		else
			Session::forget('dtoken');
	}

	public function displayFiles()
	{
		$this->dbox->allFiles();
	}


	/*public function index(){
		$storage = new Session();
		$code = Input::get( 'code' );
	    $dropboxService = OAuth::consumer( 'Dropbox' );

		if (!empty($code)) {

		    $token = $dropboxService->requestAccessToken($code);
		    Session::put("dtoken",$token->getAccessToken());
		    $result = json_decode($dropboxService->request('/account/info'), true);
		    return Redirect::to('dropbox_getfile/index');
		
		} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	 		$url = $dropboxService->getAuthorizationUri();
		    return Redirect::to( (string)$url );
		} else {
			$url = Request::url() . '?go=go';
		    return Redirect::to($url);
		}
	}*/


	//get path of files
	public function listFiles($path)
	{
		$client = new dbx\Client(Session::get('dtoken'),'FuseProject/1.0');
		$paths = new dbx\Path(); //dropbox
		$print = array(); //contains metadata of file
		$content = array(); //subfolders

		
		if($path == 'index') //get client files
			//{
				$file = $client->getMetadataWithChildren('/');
				//$dpath = "Dropbox > ";
			//}
		else
		//{
				$file = $client->getMetadataWithChildren('/'.$path);
				//$dpath = "Dropbox " . str_replace('/', '> ',$path);
				//Session::put('rpath',$path);
		//}
			
		foreach ($file['contents'] as $files) 
		{	
			$name = $paths->getName($files['path']);
			$print['file_name'] = $name;
			$print['file_id'] = $files['rev'];
			$print['file_path']= $files['path'];
			$print['root'] = $files['root'];

			$print['file_size'] = $files['size'];
			$print['file_modified'] = $client->parseDateTime($files['modified'])->format('m/d/Y g:i A');
			
				if($files['is_dir'] == 1){
					$print['file_type'] = 'Folder';
					$print['file_path']= '/'.rawurlencode($files['path']);
				}else
					$print['file_type'] = $files['mime_type'];

			array_push($content,$print);
			//Session::put('dpath',$dpath);

		}
		
		return View::make('users.index')->with(['files' => $content]);
	}

	//upload files
	public function uploadFile(){

		  if (Input::hasFile('files')) {

		  	$file = Input::file('files');
		  	$root = Input::get('parent');
		  	$filename = $file->getClientOriginalName();
		  	$destinationPath = $file->getRealPath();		//del /				
			
			// $folder = str_replace('"',"",addslashes('"files"'));
			//$uploadDestination=$destinationPath."/files/";
	    	
	    	//$uploadSuccess   = $file->move($file->getRealPath(), $filename);
		  	$dtoken = '7wxpEh8WhpAAAAAAAAAAJMzfxDW9aTfXeKssekIsm9Gjk8t5LEtFmxtSEro8pgS-';
			$client = new dbx\Client($dtoken,'FuseProject/1.0');
			$f = fopen($file->getRealPath(), "rb");
			$s = filesize($file->getRealPath());
			$result = $client->uploadFileChunked($root."/".$filename, dbx\WriteMode::add(), $f, $s);
		  	fclose($f);

		  	echo "<pre>";
		  	print_r($result);

		  	/*if($result){
		  		return Redirect::to('dropbox_getfile/index')->with('message', 'Successfully uploaded file!');	
		  		}*/

			}
	}

	public function evalAction(){
		$files = Input::all();

			if(Input::get("action")=='del'){
				$action = $this->deleteFile($files); //change this
				$msg = "Successfully deleted.";
			}

			if(Input::get("action")=='down'){
				$action = $this->downloadFile($files);
				$msg = "Successfully downloaded! Please check 'Downloads' folder.";
			}

		return Redirect::to('dropbox_getfile/index')->with('message',$msg);
	}

	public function deleteFile($files){ //change this
		$client = new dbx\Client(Session::get('dtoken'),'FuseProject/1.0');
		
		foreach ($files['check_list'] as $de) {
			$spath = str_replace(array('%2F','%5C', '%252F','%255C'), '/', $de);
			$fpath = str_replace('//', '/', $spath);
			$delPath = $client->delete($fpath);

			//$delPath = $client->doPost('/fileops/delete','auto', '/cara/tumblr_mf02p667VF1qmuidco1_500.jpg');

			}
	}

	public function downloadFile($files){
		$client = new dbx\Client(Session::get('dtoken'),'FuseProject/1.0');
		
		foreach ($files['check_list'] as $d) {
			$f= fopen('Downloads'.$d, "wb");
			$spath = str_replace(array('%2F','%5C', '%252F','%255C'), '/', $d);
			$fpath = str_replace('//', '/', $spath);
			
			$metadata = $client->getFile($fpath, $f);
			fclose($f);
			}
			
	}


	public function createFolder(){
		$client = new dbx\Client(Session::get('dtoken'),'FuseProject/1.0');
		
		$path = Session::get('rpath');
		// var_dump($path.'/');

		$fname = Input::get('folderName');
		$newFolder = $client->createFolder('/'.$fname,$path.'/');

		if($newFolder){
			return Redirect::to('dropbox_getfile/index');
		}
		
		
	}

	


}


?>