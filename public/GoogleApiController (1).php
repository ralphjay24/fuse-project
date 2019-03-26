<?php

use App\Controller;
//use \Google as g;

class GoogleApiController extends \BaseController{
	
	function google_getFile()
	{
		$file1 = Session::get('files');
		$quotas = Session::get('quota');



	// //upload sample********************************************************************************
	// $uploadsample = Session::get('upload');

	// $file = new Google_Service_Drive_DriveFile();
	// $file->setTitle('Knight Artorias');
	// $file->setDescription('picture of Knight Artorias');
	// $file->setMimeType('image/jpeg');

 //  	try {
 //    $data = file_get_contents("D:\sample\knightartorias.jpg");
 //  	//$service = new Google_Service_Drive_DriveFile($uploadsample);

	// $createdFile = $uploadsample->files->insert($file, array(
 //      'data' => $data,
 //      'mimeType' => 'text/plain',
 //    ));

 //    //echo 'File ID: %s' % $createdFile->getId();
 //    //return $createdFile;

 //   } catch (Exception $e) {
 //     echo "An error occurred: " . $e->getMessage();
 //   }
	// // $uploadsample = Session::get('upload');
	// // $file = new Google_Service_Drive_DriveFile();
	// // $file->setTitle("Knight Artorias");
	// // $result = $uploadsample->files->insert($file, array(
 // 	//  		'data' => file_get_contents("D:/sample/knightartorias.jpg"),
 // 	//  		'mimeType' => 'image/jpeg',
 // 	//  		'uploadType' => 'multipart'
	// // ));
	// //end of upload sample***********************************************************************







	// get quota or Total memory given/used******************************************************
	$total = number_format($quotas['quotaBytesTotal']/1000000000);
  	$used = number_format($quotas['quotaBytesUsed']/1000000000);
	
		/*foreach ($file['items'] as $files) 
		{
			echo $files['title'].'<br>';
		}*/
	echo "Total quota: ".$total."GB";
	echo "<pre>";


	if($used > 0)
	echo "Used quota: ".$used."GB";
	else
	$used = number_format($quotas['quotaBytesUsed']/1000000000,3,".",".");
	echo "Used quota: ".$used."MB";
	// end of get quota or Total memory given/used**********************************************




	//get file**********************************************************************************
		$content = array();
		foreach ($file1['items'] as $files) 
		{
			$print['file_id'] = $files['id'];
			$print['file_name'] = $files['title'];
			if(!empty($files['fileSize']))
				$print['file_size'] = $files['fileSize'];
			else
				$print['file_size'] = 0;
			    $print['file_modified'] = $files['modifiedDate'];
			
			if($files['mimeType'] == 'application/vnd.google-apps.folder')
				$print['file_type'] = 'Folder';
			else
				$print['file_type'] = $files['mimeType'];
			array_push($content,$print);
		}	
		//print_r($content);
		echo "</pre>";
		return View::make('users.index')->with(['files' => $content]);
	//end of get file**************************************************************************
	}


	// function insertFile() 
	// {
 //  	 $file = new DriveFile();
 //  	 $file->setTitle("Knight Artorias");
 //  	 $file->setDescription("This is Knight Artorias");
 // 	 $file->setmimeType('application/vnd.google-apps.drive-sdk');
 //  	 $createdFile = $service->files->insert($file);
 // 	 echo "File ID: " . $file->getId();
	// }

	function uploadFile()
	{

	//set_include_path(get_include_path() . PATH_SEPARATOR . '/path/to/google-api-php-client/src');
	
	$uploadsample = Session::get('upload');
	try{
	$file = new Google_Service_Drive_DriveFile();
	$file->setTitle('Knight Artorias');
	$file->setDescription('Sample Document');
	$result = $uploadsample->files->insert($file, array(
  		'data' => file_get_contents("D:/sample/test.txt"),
  		'mimeType' => 'text/plain',
  		'uploadType' => 'multipart'
	));
}catch (Exception $e){
	print "An Error occured:". $e->getMessage();
}

	}

function deleteFile() {
  
	$deletesample = Session::get('files');
  try {
    var_dump($deletesample->files->delete('0B1oSvjV2m0gQdzR2SFJUUFplMXM'));
    //return View::make('users.index');
  } catch (Exception $e) {
    print "An error occurred: " . $e->getMessage();
  }
}
/**
 * Insert new file.
 *
 * @param Google_DriveService $service Drive API service instance.
 * @param string $title Title of the file to insert, including the extension.
 * @param string $description Description of the file to insert.
 * @param string $parentId Parent folder's ID.
 * @param string $mimeType MIME type of the file to insert.
 * @param string $filename Filename of the file to insert.
 * @return Google_DriveFile The file that was inserted. NULL is returned if an API error occurred.
 */
// function insertFile($service, $title, $description, $parentId, $mimeType, $filename) {
//   $file = new Google_DriveFile();
//   $file->setTitle($title);
//   $file->setDescription($description);
//   $file->setMimeType($mimeType);

//   // Set the parent folder.
//   if ($parentId != null) {
//     $parent = new Google_ParentReference();
//     $parent->setId($parentId);
//     $file->setParents(array($parent));
//   }

//   try {
//     $data = file_get_contents($filename);

//     $createdFile = $service->files->insert($file, array(
//       'data' => $data,
//       'mimeType' => $mimeType,
//     ));

//     // Uncomment the following line to print the File ID
//     // print 'File ID: %s' % $createdFile->getId();

//     return $createdFile;
//   } catch (Exception $e) {
//     print "An error occurred: " . $e->getMessage();
//   }
// }


// /**
//  * Download a file's content.
//  *
//  * @param Google_DriveService $service Drive API service instance.
//  * @param File $file Drive File instance.
//  * @return String The file's content if successful, null otherwise.
//  */
// function downloadFile($service, $file) {
//   $downloadUrl = $file->getDownloadUrl();
//   if ($downloadUrl) {
//     $request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
//     $httpRequest = Google_Client::$io->authenticatedRequest($request);
//     if ($httpRequest->getResponseHttpCode() == 200) {
//       return $httpRequest->getResponseBody();
//     } else {
//       // An error occurred.
//       return null;
//     }
//   } else {
//     // The file doesn't have any content stored on Drive.
//     return null;
//   }
// }


/**
 * Update an existing file's metadata and content.
 *
 * @param Google_DriveService $service Drive API service instance.
 * @param string $fileId ID of the file to update.
 * @param string $newTitle New title for the file.
 * @param string $newDescription New description for the file.
 * @param string $newMimeType New MIME type for the file.
 * @param string $newFilename Filename of the new content to upload.
 * @param bool $newRevision Whether or not to create a new revision for this file.
 * @return Google_DriveFile The updated file. NULL is returned if an API error occurred.
 */
// function updateFile($service, $fileId, $newTitle, $newDescription, $newMimeType, $newFileName, $newRevision) {
//   try {
//     // First retrieve the file from the API.
//     $file = $service->files->get($fileId);

//     // File's new metadata.
//     $file->setTitle($newTitle);
//     $file->setDescription($newDescription);
//     $file->setMimeType($newMimeType);

//     // File's new content.
//     $data = file_get_contents($newFileName);

//     $additionalParams = array(
//         'newRevision' => $newRevision,
//         'data' => $data,
//         'mimeType' => $newMimeType
//     );

//     // Send the request to the API.
//     $updatedFile = $service->files->update($fileId, $file, $additionalParams);
//     return $updatedFile;
//   } catch (Exception $e) {
//     print "An error occurred: " . $e->getMessage();
//   }
// }



/**
 * Permanently delete a file, skipping the trash.
 *
 * @param Google_DriveService $service Drive API service instance.
 * @param String $fileId ID of the file to delete.
 */
// function deleteFile($service, $fileId) {
//   try {
//     $service->files->delete($fileId);
//   } catch (Exception $e) {
//     print "An error occurred: " . $e->getMessage();
//   }
// }


// function getQuota() {

//   	$quotas = Session::get('quota');
//   	$total = $quotas['quotaBytesTotal']/1000000000;
//   	$used = $quotas['quotaBytesUsed']/1000000000;

//     echo "Total quota (bytes):".$total."/n";
//     echo "Used quota (bytes):".$used."/n";

// }
	
}
?>