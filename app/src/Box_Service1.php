<?php

class Box_Service{
	
	private $boxService;

	public function __construct()
	{

		$this->boxService = OAuth::consumer('Box');
	}

	public function boxAuth()
	{
		return $this->boxService->getAuthorizationUri();
		
	}
	public function login($code,$state)
	{
		$s = isset($state) ? $state : null;

		$token = $this->boxService->requestAccessToken($code,$s);
		return $token;
	}

	public function createFolder($foldername,$folderId)
	{
		$body = array('name' => $foldername, 'parent' => array('id'=>$folderId['id']));
		$result = $this->boxService->request('/folders','POST',json_encode($body));
		return $result;
	}

	public function searchFile($filename,$filetype)
	{
		return json_decode($this->boxService->request('/search?query='.$filename.'&type='.$filetype),true);
	}

	public function getFolder($folderId)
	{
		return json_decode($this->boxService->request('/folders/'.$folderId),true);
	}

	public function getFiles($folderId)
	{
		
		return json_decode($this->boxService->request('/folders/'.$folderId.'/items'),true);

	}

	public function getFile($fileId)
	{
		return json_decode($this->boxService->request('/files/'.$fileId),true);
	}

	public function allFiles($folder_path)
	{
		$print = array();
		$content = array('items'=>array());

		if($folder_path === "My_Drive")
			$search = $this->searchFile('Fuse Storage','folder');
		else
			$search = $this->searchFile($folder_path,'folder');

		if(!empty($search))
		{
			$list = $this->getFiles($search['entries'][0]['id']);
			
			foreach ($list['entries'] as $files) {
				$file = $this->getFile($files['id']);
				$print['file_id'] = $file['id'];
	            $print['file_name'] = $file['name'];
	            $print['file_type'] = $this->fileType($file['name']).' File';
	            $print['file_size'] = $this->formatBytes($file['size']);
	            $print['file_modified'] = date("F j, Y, g:i a",strtotime($file['modified_at']));
	            $print['location'] = 'Box';
	            $content['parent_id'] = $file['parent']['id'];
	            array_push($content['items'], $print);
				
			}
			$content['parent_id'] = $search['entries'][0]['id'];
		}
		return $content;
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
}