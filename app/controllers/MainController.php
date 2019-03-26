<?php

class MainController extends BaseController {

	public function __construct(Google_Service $gd, Dropbox_Service $dbox, Box_Service $box)
    {
    	if(Session::has('user'))
    	{
	        $this->gd = $gd;
	        $this->dbox = $dbox;
	        $this->box = $box;
	    }
	    else
	    	return Redirect::to('login')->with('global','Need to login');

	     
    } 

    function index()
    {
    	if(Session::has('user'))
    	{
	    	$user = DB::table('users')->where('user_id', Session::get('user')->user_id)->first();
	    	$token = DB::table('token')->where('user_id', Session::get('user')->user_id)->first();
	    	if($user->isGoogle == 'true')
	    	{	
	    		return Redirect::to('/google');
	    	}
	    	elseif($user->isDropbox == 'true')
	    	{
	    		return Redirect::to('/dropbox');
	    	}
	    	
	    	elseif($user->isBox == 'true')
	    	{	
	    		return Redirect::to('/box-signin');
	    	}

	    	else
	    	{	
	    		if (!empty($token->gtoken)) 
	    		{
	    			$gtoken = $this->gd->refreshToken($token->gtoken);
	    			Session::put('gtoken',['token' => $gtoken, 'refresh' => $token->gtoken]);
	    			
	    			$search = $this->gd->searchFile("Fuse Storage");
			        
			        if(empty($search))
			         	$this->gd->createFolder('root','Fuse Storage');
	    		}
	    		
	    		if(!empty($token->dtoken))
	    		{	
	    			Session::put('dtoken',$token->dtoken);
	    			$search = $this->dbox->search($token->dtoken, 'Fuse Storage');
					if(empty($search))
						$this->dbox->createFolder($token->dtoken, '/Fuse Storage');
	    		}

	    		
	    		if(!empty($token->btoken))
	    		{
	    			$btoken = $this->box->refresh_token($token->btoken);
	    			
	    			Session::put('btoken',['token' => $btoken, 'refresh' => $btoken['refresh_token']]);
	    			DB::table('token')
						->where('user_id', Session::get('user')->user_id)
						->update(['btoken' => $btoken['refresh_token']]);
	    			$search = $this->box->search_file('Fuse Storage','folder');
			
					if(empty($search['entries'])){
						$folder = $this->box->create_folder('Fuse Storage',0);
					}
	    		}


	    			
	    		//echo $user->dtoken;
	    		return Redirect::to('home/My_Drive');
	    	}
	    }
	    else
	    	return Redirect::to('login')->with('global','Need to login');
    }

    function addStorage($storage)
    {
    	if(Session::has('user'))
    	{
	    	if($storage == 'google')
	    	{
	    		Session::push('upload','google');
	    		return Redirect::to('/google');
	    	}
	    	elseif($storage == 'dropbox')
	    	{
	    		Session::push('upload','dropbox');
	    		return Redirect::to('/dropbox');
	    	}
	    	elseif($storage == 'box')
	    	{
	    		Session::push('upload','box');
	    		return Redirect::to('/box-signin');
	    	}
	    	else
	    		return Redirect::to('/home/My_Drive');
	    }
	    else
	    	return Redirect::to('login')->with('global','Need to login');
    }

	function display($folder_path)
	{
		if(Session::has('user'))
		{
			$files = array('files'		=> array(), 
						   'folders'	=> array(), 
						   'gparent_id' => '', 
						   'dparent_id' => '', 
						   'bparent_id' => ''
						   );
			$memory_total = 0;
			$memory_used = 0;
			$parents = array();
			
			if($folder_path == 'My_Drive')
				Session::put('path',$folder_path);
			else
			{
				$path = Session::get('path');
				$pos = strpos($path, $folder_path);
				if($pos !== false)
				{
					$str = strstr($path, $folder_path,true);
					Session::put('path',$str.$folder_path);
				}
				else
					Session::put('path',$path.'/'.$folder_path);
			}

			if(Session::has('gtoken'))
			{
				if($this->gd->isLoggedIn())
					$google = $this->gd->allFiles($folder_path);
				
				if(!empty($google['items']))
				{
					foreach ($google['items'] as $g) 
					{
						if($g['file_type'] == 'Folder')
							array_push($files['folders'],$g);
						else
							array_push($files['files'],$g);
					}
				}
				
				$gquota = $this->gd->quotaInfo();
				$memory_used  += $gquota['used']; 
				$memory_total += $gquota['total'];

				if(!empty($google['parent_id']))
				{
					//array_push($files['upload'], 'google');
					$files['gparent_id'] = $google['parent_id'];
					$parents['google'] = $google['parent_id'];
				}

			}
			if (Session::has('dtoken')) {
				
				$dropbox = $this->dbox->allFiles(Session::get('dtoken'),$folder_path);
				if(!empty($dropbox['items']))
				{
					foreach ($dropbox['items'] as $d) 
					{
						if($d['file_type'] == 'Folder')
							array_push($files['folders'],$d);
						else
							array_push($files['files'],$d);
					}
				}

				$dquota = $this->dbox->quotaInfo(Session::get('dtoken'));
				$memory_used  += $dquota['used']; 
				$memory_total += $dquota['total'];

				if(!empty($dropbox['parent_id']))
				{
					//array_push($files['upload'], 'dropbox');
					$files['dparent_id'] = $dropbox['parent_id'];
					$parents['dropbox'] = $dropbox['parent_id'];
				}
			}

			if (Session::has('btoken')) {
				if($this->box->isLoggedIn())
					$box_storage = $this->box->allFiles($folder_path);
				if(!empty($box_storage['items']))
				{
					foreach ($box_storage['items'] as $b) 
					{
						if($b['file_type'] == 'Folder')
							array_push($files['folders'],$b);
						else
							array_push($files['files'],$b);
					}
				}
				
				$bquota = $this->box->quotaInfo();
				$memory_used  += $bquota['used']; 
				$memory_total += $bquota['total'];

				if(!empty($box_storage['parent_id']))
				{
					//array_push($files['upload'], 'box');
					$files['bparent_id'] = $box_storage['parent_id'];
					$parents['box'] = $box_storage['parent_id'];
				}
			}
			$this->savingFiles($files,$parents);
			
			$memory_total = $this->formatBytes($memory_total);
			$memory_used = $this->formatBytes($memory_used);
			$files['quota'] = ['used' => $memory_used, 'total' => $memory_total];
			Session::put('quota',['used' => $memory_used, 'total' => $memory_total]);
			Session::put('parents',$parents);

			$upload = json_encode(Session::get('upload'));	
			DB::table('upload')
				->where('user_id', Session::get('user')->user_id)
				->update(['last_session' => $upload]);
				
			
			if(!empty($files['files']) || !empty($files['folders']))
				return View::make('users.index')->with(['files' => $files]);
			else
				return View::make('users.index')->with(['files' => $files, 'message' => 'Welcome to Fuse! You currently have no files yet.']);
		}
		else
			return Redirect::to('login')->with('global','Need to login');
		
	}

	private function savingFiles($files,$parents)
	{
		foreach ($files['folders'] as $file) 
			{	
				$search = Files::find($file['file_id']);

				if(empty($search->file_id))
				{
					$folders = new Files;
					$folders->file_id 		= $file['file_id'];
					$folders->file_name 	= $file['file_name'];
					$folders->file_type 	= $file['file_type'];
					$folders->file_modified = $file['file_modified'];
					$folders->file_size 	= $file['file_size'];
					$folders->location 		= $file['location'];
					$folders->parent_id 	= $parents[strtolower($file['location'])];
					$folders->user_id		= Session::get('user')->user_id;
					$folders->save();
				}
			}

			foreach ($files['files'] as $file) 
			{
				$search = Files::find($file['file_id']);

				if(empty($search->file_id))
				{
					$f = new Files;
					$f->file_id 		= $file['file_id'];
					$f->file_name 		= $file['file_name'];
					$f->file_type 		= $file['file_type'];
					$f->file_modified 	= $file['file_modified'];
					$f->file_size 		= $file['file_size'];
					$f->location 		= $file['location'];
					$f->parent_id 		= $parents[strtolower($file['location'])];
					$f->user_id		    = Session::get('user')->user_id;
					$f->save();
				}
			}
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

	function uploadFile()
	{
		if(Session::has('user'))
		{
			
			$files = Input::file('files');
			//dd($files);
			$redirect = substr(strrchr(Input::get('redirect'), "/"), 1);

			$parent = array();
			$parent['google'] = Input::get('gparent');
			$parent['dropbox'] = Input::get('dparent');
			$parent['box'] = Input::get('bparent');

			if(Session::has('gtoken'))
			{
				
				if($this->gd->isLoggedIn())
					$gquota = $this->gd->quotaInfo();
			}
			if(Session::has('dtoken'))	
				$dquota = $this->dbox->quotaInfo(Session::get('dtoken'));
			if(Session::has('btoken'))
			{	
				if($this->box->isLoggedIn())
					$bquota = $this->box->quotaInfo();
			}

				
			
			
			$storage = Session::get('upload');
			
			foreach ($storage as $storages) {
				
				if(!empty($parent[$storages]))
				{
					$key = array_search($storages, $storage);
					break;
				}
				else 
					$key = 0;
			}
			
			
			//echo "<pre>";
			//print_r($files);
			foreach($files as $file)
			{

				
				$validator = Validator::make(['file' => $file],[ 'files' => '']);
				//$validator = true;
				if($validator->fails())
				{
					return Redirect::to('/home/'.$redirect)->withErrors($validator)->withInput();
				}
				else
				{
					
					if($file->getSize() <= ($gquota['total']-$gquota['used']) && $storage[$key] == 'google')
					{
						if($this->gd->isLoggedIn())
							$createdFile = $this->gd->resumableUpload($file,$parent['google']);
					}
					elseif($file->getSize() <= ($dquota['total']-$dquota['used']) && $storage[$key] == 'dropbox')
						$createdFile = $this->dbox->resumableUpload(Session::get('dtoken'),$file,$parent['dropbox']);
					elseif($file->getSize() <= ($bquota['total']-$bquota['used']) && $storage[$key] == 'box')
					{
						if($this->box->isLoggedIn())
							$createdFile = $this->box->upload_file($file,$parent['box']);
					}
					
				}
				if( 
					(!empty($parent['google']) && !empty($parent['dropbox'])) || 
					(!empty($parent['dropbox']) && !empty($parent['box'])) || 
					(!empty($parent['box']) && !empty($parent['google'])) 
				)
				{
					$upload = array_shift($storage);
					array_push($storage, $upload);
				}
				
			}
			Session::put('upload',$storage);
			
			return Redirect::to('/home/My_Drive');		
		}
		else
			return Redirect::to('login')->with('global','Need to login');
	}


	function evaluateAction(){
		$ids= Input::get('check_list');
		$action = Input::get("action");
		$files = array();


		foreach ($ids as $id) 
		{
			array_push($files, DB::table('files')->where('file_id','=',$id)->where('user_id', Session::get('user')->user_id)->get());
			
		}
		if($action == 'delete')
		{
			$this->delete($files);
			DB::table('files')->where('file_id','=',$id)->where('user_id', Session::get('user')->user_id)->delete();
		}

		elseif($action == 'download')
		{
			$dl = $this->download($files);
			
		}
		return Redirect::to('home/My_Drive');	
	}
	

	// function download()
	// {
	// 	if(Session::has('user'))
	// 	{
	// 		$response = "";
	// 		$fileId = Input::get('file_id');
	// 		$storage = Input::get('file_location');
	// 		$name = Input::get('file_name');
	// 		$redirect = substr(strrchr(Input::get('redirect'), "/"), 1);

	// 		if($storage == 'Google')
	// 		{
	// 			if($this->gd->isLoggedIn())
	// 				$download = $this->gd->downloadFile($fileId);
	// 		}
	// 		elseif($storage == 'Dropbox')
	// 			$download = $this->dbox->downloadFile(Session::get('dtoken'),$fileId,$name);
	// 		elseif($storage == 'Box')
	// 		{
	// 			if($this->box->isLoggedIn())
	// 				$download = $this->box->download_file($fileId);
	// 			return Redirect::to($download)->with('response',$response);
	// 		}
			
	// 		if($download){
	// 			$response = "success";
	// 		}else{
	// 			$response = "failed";
	// 		}
			
	// 		return Redirect::to('/home/'.$redirect)->with('response',$response);

			
		
	// 	}else
	// 		return Redirect::to('login')->with('global','Need to login');
	// }

	function download($files)
	{
		if(Session::has('user'))
		{
			//dd($files);
			$filenames = array();

			foreach($files as $fil)
			{
				$fileId = $fil[0]->file_id;
				$storage = $fil[0]->location;
				$name = $fil[0]->file_name;

				if($storage == 'Google')
				{
					if($this->gd->isLoggedIn())
						$this->gd->downloadFile($fileId);
				}
				elseif($storage == 'Dropbox')
					$this->dbox->downloadFile(Session::get('dtoken'),$fileId,$name);
				elseif($storage == 'Box')
				{
					if($this->box->isLoggedIn())
						$download = $this->box->download_file($fileId,$name);
					//return Response::download($download);
				}
				array_push($filenames, $name);
			}
			echo "<pre>";
			dd($download);
			//$this->zipfiles($filenames);
		 //return Redirect::to('home/My_Drive');
		
		}else
			return Redirect::to('login')->with('global','Need to login');
	}

	function zipfiles($files)
	{
		
		$zipname = date("FjYgia",time()).'fuse.zip';
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);
		foreach ($files as $file) {
		  $zip->addFile('Downloads/'.Session::get('user')->user_id.'/'.$file,$file);
		}
		$zip->close();

		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$zipname);
		header('Content-Length: ' . filesize($zipname));
		readfile($zipname);

	}

	function delete($files)
	{
		if(Session::has('user'))
		{
			foreach($files as $fil){
				$fileId = $fil[0]->file_id;
				$location = $fil[0]->file_location;
				$file_type = $fil[0]->file_type;
			
			if($location == 'Google')
			{
				if($this->gd->isLoggedIn())
					$this->gd->deleteFile($fileId);
			}
			if($location == 'Dropbox') 
			{
				$this->dbox->deleteFile(Session::get('dtoken'),$fileId);
			}
			if($location == 'Box')
			{	
				if($file_type == 'Folder')
				{
					if($this->box->isLoggedIn())
					 	$this->box->delete_folder($fileId);
				}
				else
				{
					if($this->box->isLoggedIn())
						$this->box->delete_file($fileId);
				}

			}
		}
		return Redirect::to('home/My_Drive');

		}
		else
			return Redirect::to('login')->with('global','Need to login');

	}

	
	function createFolder()
	{
		if(Session::has('user'))
		{	
			$storage=array();
			if(Session::has('gtoken'))
				array_push($storage,'google');
			if(Session::has('dtoken'))
				array_push($storage,'dropbox');
			if(Session::has('btoken'))
				array_push($storage,'box');


			if(Input::has('foldername'))
				$foldername = Input::get('foldername');
			else
				$foldername = 'New Folder';


			$parent = Input::get('parent');
			$redirect = substr(strrchr(Input::get('redirect'), "/"), 1);
			
			foreach ($storage as $storages) {
				if($storages == 'google')
				{	
					if($this->gd->isLoggedIn())
						$folder = $this->gd->createFolder($parent['google'],$foldername);
				}
				if ($storages == 'dropbox')
					$folder = $this->dbox->createFolder(Session::get('dtoken'),$parent['dropbox'].'/'.$foldername);
				if($storages == 'box')
				{
					if($this->box->isLoggedIn())
						$folder = $this->box->create_folder($foldername,$parent['box']);
				}
			}
			return Redirect::to('/home/'.$redirect);
		}
	}	

}