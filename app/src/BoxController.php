<?php


class BoxController extends \BaseController{

// user functions ******************************************************************************************************************

	//authorize, saves token
	function BoxSignin(){
		include('BoxOfficialPHPLibrary.php');
			
			$box = new Box_API();
			
			if(!$box->load_token()){
				if(isset($_GET['code'])){
					$token = $box->get_token($_GET['code'], true);
					if($box->write_token($token, 'file')){
						$box->load_token();
					}
				} else {
					$box->get_code();
				}
			}

			return Redirect::to('box');		
	}

	function BoxLogout(){
		$f = @fopen("token.box", "r+");
			if ($f !== false) {
			    ftruncate($f, 0);
			    fclose($f);
			return Redirect::to('/');
			}
	}




// Box Root Folder******************************************************************************************************************
	function RootFolder(){

		include('BoxOfficialPHPLibrary.php');
			
			$box = new Box_API();
			
			//loads
			if(!$box->load_token()){
				if(isset($_GET['code'])){
					$token = $box->get_token($_GET['code'], true);
					if($box->write_token($token, 'file')){
						$box->load_token();
					}
				} else {
					$box->get_code();
				}
			}


			$rs = Session::get('refresh');
			if($rs == NULL){
				$refreshcount = 1;
				Session::put('refresh',$refreshcount);
				$page = $_SERVER['PHP_SELF'];
				$sec = "1";
				header("Refresh: $sec; url=$page");
			}


			// use details, get quota
			$user = $box->get_user();
			$totalquota = $box->formatSizeUnits($user['space_amount']);
			$quotaused = $box->formatSizeUnits($user['space_used']);
			echo "Total Quota: ".$totalquota."<br />";
			echo "Quota used: ".$quotaused."<br />";

			
			// get all folders of the root folder (the drive folder itself), 0 is the id of the root folder of box drive
			$rootfolder = $box->get_folders('0');


			// All Files in a particular folder
			$allfiles = $box->get_files('0');


			echo "<br /> <br />All Files";



			$filescontent = array();
			 foreach ($allfiles as $files)
			 {
				$at = Session::get('accessToken');
			 	$array['accessToken'] = $at;

			 	$array['file_id'] = $files['id'];
			 	 $filedetail = $box->get_file_details($files['id']);
			 	 $array['file_modified_at'] = $filedetail['content_modified_at'];	
			 	  $filesize = $box->formatSizeUnits($filedetail['size']);	 	
			 	  $array['file_size'] = $filesize;
			 	$array['file_name'] = $files['name'];
			 	$array['file_type'] = $files['type'];


			 	array_push($filescontent,$array);
			 }


			$foldercontent = array();
			 foreach ($rootfolder as $folder)
			 {

			 	$array['folder_id'] = $folder['id'];
			 	 $folderdetail = $box->get_folder_details($folder['id']);
			 	 $array['folder_modified_at'] = $folderdetail['modified_at'];
			 	  $foldersize = $box->formatSizeUnits($folderdetail['size']);
			 	  $array['folder_size'] = $foldersize;
			 	$array['folder_name'] = $folder['name'];
			 	$array['folder_type'] = $folder['type'];

			 	array_push($foldercontent,$array);
			 }
			return View::make('users.boxrootindex')->with(['folder' => $foldercontent])->with(['files' => $filescontent]);
	}

	function BoxRootFileDelete($id){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}

		// Delete file
		$box->delete_file($id);
		return Redirect::to('box');
	}

	function BoxRootFolderDelete($id){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}


		// Delete folder
		$opts['recursive'] = 'true';
		$box->delete_folder($id, $opts);

		return Redirect::to('box');
	}

	function BoxRootCreateFolder(){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}


		$input = Input::all();
		$foldername = $input['foldername'];

		$box->create_folder($foldername, '0');

		return Redirect::to('box');
	}

	function BoxRootUploadFile(){
		include('BoxOfficialPHPLibrary.php');
			
			$box = new Box_API();
			
			if(!$box->load_token()){
				if(isset($_GET['code'])){
					$token = $box->get_token($_GET['code'], true);
					if($box->write_token($token, 'file')){
						$box->load_token();
					}
				} else {
					$box->get_code();
				}
			}


			$file = Input::file('file');
			$filename = $file->getClientOriginalName();
		

			// checks file if existed in the folder
			$cfile = $box->get_files('0');

			//var_dump($cfile);

			$foundfile = array();	
			$found = 0;
			foreach($cfile as $ccfile){
				if($ccfile['name'] == $filename){
					$found = 1;
					$array['file_id'] = $ccfile['id'];
					$array['file_name'] = $ccfile['name'];
					array_push($foundfile,$array);
				}else{
					$found = 0;
				}
			}




			if ($found > 0){
				/*deletes the existing file then uploads the new file*/
				foreach($foundfile as $ff){
					echo $ff['file_id'];
					echo $ff['file_name'];
					$box->delete_file($ff['file_id']);
				}

				$destinationPath = public_path()."/uploads/box/";
				$file->move($destinationPath, $filename);
				$filetobeuploaded = $destinationPath.$filename;
				$box->upload_file($filetobeuploaded, '0');

				return Redirect::to('box');

			}elseif ($found == 0) {
				/*uploads the file*/
				$destinationPath = public_path()."/uploads/box/";
				$file->move($destinationPath, $filename);
				$filetobeuploaded = $destinationPath.$filename;
				$box->upload_file($filetobeuploaded, '0');

				return Redirect::to('box');			
			} 
			
	}




// Box Opened Folder******************************************************************************************************************
	function BoxFolderOpen($id){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}


		// user details, get quota
		$user = $box->get_user();
		$totalquota = $box->formatSizeUnits($user['space_amount']);
		$quotaused = $box->formatSizeUnits($user['space_used']);
		echo "Total Quota: ".$totalquota."<br />";
		echo "Quota used: ".$quotaused."<br />";


		//Folder Opened
		$rootfolder = $box->get_folders($id);
		$folder_details = $box->get_parent_folder($id);	
		$folder_details2 = $box->get_folder_details($id);


		Session::put("parentfolder",$id);
		Session::put("folderopened",$id);


		//Breadcrumbs
		echo "<br /> <br />".$folder_details[4].">".$folder_details2['name'];


		// All Files in a particular folder
		$allfiles = $box->get_files($id);

		$OpenedFolderfilescontent = array();
		 foreach ($allfiles as $files)
		 {
			$at = Session::get('accessToken');
			$array['accessToken'] = $at;
			
		 	$array['file_id'] = $files['id'];
			 $filedetail = $box->get_file_details($files['id']);
			 $array['file_modified_at'] = $filedetail['modified_at'];
			  $filesize = $box->formatSizeUnits($filedetail['size']);	 	
			  $array['file_size'] = $filesize;
		 	$array['file_name'] = $files['name'];
		 	$array['file_type'] = $files['type'];
		 	$array['file_parentfolder'] = $files['parent'];

		 	array_push($OpenedFolderfilescontent,$array);
		 }


		$OpenedFoldercontent = array();
		 foreach ($rootfolder as $folder)
		 {

		 	$array['folder_id'] = $folder['id'];
			 $folderdetail = $box->get_folder_details($folder['id']);
			 $array['folder_modified_at'] = $folderdetail['modified_at'];
			  $foldersize = $box->formatSizeUnits($folderdetail['size']);
			  $array['folder_size'] = $foldersize;
		 	$array['folder_name'] = $folder['name'];
		 	$array['folder_type'] = $folder['type'];
		 	$array['parent_folder'] = $folder['parent'];

		 	array_push($OpenedFoldercontent,$array);
		 }


		return View::make('users.boxindex')->with(['Openedfolder' => $OpenedFoldercontent])->with(['Openedfolderfiles' => $OpenedFolderfilescontent]);
	}

	function BoxFileDelete($id){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}


		// Delete file
		$box->delete_file($id);

		//Folder Opened
		$folderopened = Session::get('folderopened');
		return Redirect::to('boxFolderOpen/'.$folderopened);
	}

	function BoxFolderDelete($id){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}


		// Delete folder
		$opts['recursive'] = 'true';
		$box->delete_folder($id, $opts);


		//Folder Opened
		$folderopened = Session::get('folderopened');
		return Redirect::to('boxFolderOpen/'.$folderopened);
	}

	function BoxCreateFolder(){
		include('BoxOfficialPHPLibrary.php');

		$box = new Box_API();
		
		if(!$box->load_token()){
			if(isset($_GET['code'])){
				$token = $box->get_token($_GET['code'], true);
				if($box->write_token($token, 'file')){
					$box->load_token();
				}
			} else {
				$box->get_code();
			}
		}


		$input = Input::all();
		$foldername = $input['foldername'];
		$parentfolder = Session::get('parentfolder');


		$box->create_folder($foldername, $parentfolder);

		//Folder Opened
		$folderopened = Session::get('folderopened');
		return Redirect::to('boxFolderOpen/'.$folderopened);
	}

	function BoxUploadFile(){
		include('BoxOfficialPHPLibrary.php');
			
			$box = new Box_API();
			
			if(!$box->load_token()){
				if(isset($_GET['code'])){
					$token = $box->get_token($_GET['code'], true);
					if($box->write_token($token, 'file')){
						$box->load_token();
					}
				} else {
					$box->get_code();
				}
			}


			$file = Input::file('file');
			print_r($file);
			/*$filename = $file->getClientOriginalName();
		
			//Folder Opened
			$folderopened = Session::get('folderopened');

			// checks file if existed in the folder
			$cfile = $box->get_files($folderopened);

			//var_dump($cfile);

			$foundfile = array();	
			$found = 0;
			foreach($cfile as $ccfile){
				if($ccfile['name'] == $filename){
					$found = 1;
					$array['file_id'] = $ccfile['id'];
					$array['file_name'] = $ccfile['name'];
					array_push($foundfile,$array);
				}else{
					$found = 0;
				}
			}




			if ($found > 0){
				//deletes the existing file then uploads the new file
				foreach($foundfile as $ff){
					echo $ff['file_id'];
					echo $ff['file_name'];
					$box->delete_file($ff['file_id']);
				}

				//$destinationPath = public_path()."/uploads/box/";
				//$file->move($destinationPath, $filename);
				//$filetobeuploaded = $destinationPath.$filename;
				$box->upload_file($file->getRealPath(), $folderopened);

				return Redirect::to('boxFolderOpen/'.$folderopened);

			}elseif ($found == 0) {
				//uploads the file
				//$destinationPath = public_path()."/uploads/box/";
				//$file->move($destinationPath, $filename);
				//$filetobeuploaded = $destinationPath.$filename;
				$box->upload_file($file->getRealPath(), $folderopened);

				return Redirect::to('boxFolderOpen/'.$folderopened);			
			} */
			
	}
}