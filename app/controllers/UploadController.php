<?php

class UploadController extends BaseController{
	
	private $g;

	public function __construct(Google_Service $ga)
    {
        $this->g = $ga;
    }
 
	

	public function insertFile() 
	{
		$service = $this->g->getService();
  		$file = new Google_Service_Drive_DriveFile();
  		$file->setTitle('My document');
		$file->setDescription('A test document');
		$file->setMimeType('text/plain');

		


  		// Set the parent folder.
  		/*if ($parentId != null) 
  		{
    		$parent = new Google_ParentReference();
    		$parent->setId($parentId);
    		$file->setParents(array($parent));
  		}*/

  		try {
    		$data = file_get_contents('C:\Users\Pepito\Desktop\upload.txt');

			$createdFile = $service->files->insert($file, array(
      		'data' => $data,
      		'mimeType' => 'text/plain',
      		'uploadType' => 'multipart'
    		));

    	// Uncomment the following line to print the File ID
    	print_r($createdFile);

    		//return $createdFile;
  		} catch (Exception $e) {
    		print "An error occurred: " . $e->getMessage();
  		}
	}
}