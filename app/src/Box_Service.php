<?php 
	
	error_reporting(E_ERROR);
	set_time_limit(0);
	
	class Box_Service {
		
		private $client_id 		= '';
		private $client_secret 	= '';
		private $redirect_uri	= '';
		private $access_token	= '';
		//private $refresh_token	= '';
		private $authorize_url 	= 'https://www.box.com/api/oauth2/authorize';
		private $token_url	 	= 'https://www.box.com/api/oauth2/token';
		private $api_url 		= 'https://api.box.com/2.0';
		private $upload_url 		= 'https://upload.box.com/api/2.0';

		public function __construct()
		{
			
			$this->client_id 		= Config::get('box.client_id');
			$this->client_secret	= Config::get('box.client_secret');
			$this->redirect_uri		= 'http://localhost:8000/box-signin';
			
		}
		
		/* First step for authentication [Gets the code] */
		public function get_code() {
			
				// echo $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
				$url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
				return $url;
			
		}
		
		/* Second step for authentication [Gets the access_token and the refresh_token] */
		public function get_token($code = '') {
			$url = $this->token_url;
			
			$params = array(
				'grant_type' => 'authorization_code', 
				'code' => $code, 
				'client_id' => $this->client_id, 
				'client_secret' => $this->client_secret
			);
			
			
			$token = json_decode($this->post($url, $params), true);
			$this->access_token = $token['access_token'];
			$token['created'] = time();
			return $token;
			
		}

		public function refresh_token($refresh_token)
		{
			$url = $this->token_url;
			$params = array(
				'grant_type' => 'refresh_token', 
				'refresh_token' => $refresh_token, 
				'client_id' => $this->client_id, 
				'client_secret' => $this->client_secret
			);
			$token = json_decode($this->post($url, $params), true);
			$this->access_token = $token['access_token'];
			$token['created'] = time();
			return $token;
		}

		public function isLoggedIn()
	  	{
		    if(Session::has('btoken'))
		    {
		      	$btoken = Session::get('btoken')['token'];
		      	$expiration = (time()-$btoken['created'])+1800 ;
		      
		      	if (!empty(Session::get('btoken')['token']) && $expiration <= $btoken['expires_in']) 
		      	{
		          	$this->access_token = Session::get('btoken')['token']['access_token'];
		          
		      	}
		      	else
		      	{
		          	$refresh = Session::get('btoken')['refresh'];
		          	$btoken = $this->refresh_token($refresh);
		          	Session::put('btoken',['token' => $btoken, 'refresh' => $btoken['refresh_token']]);
		          	DB::table('token')
						->where('user_id', Session::get('user')->user_id)
						->update(['btoken' => $btoken['refresh_token']]);
		      	}

		      	return true;
		    }
		    else
		      	return false;
	  	}

	  	/* Gets the current user details */
		public function get_user() {
			$url = $this->build_url('/users/me');
			return json_decode($this->get($url),true);
		}

		public function quotaInfo()
		{
			$quota = array();
			$user = $this->get_user();

			$quota['total'] = $user['space_amount']-2147483648;
      		$quota['used'] = $user['space_used'];

      		return $quota;
		}

		public function search_file($search, $type){
			if($this->isLoggedIn())
			{
				$params = array('query' => $search, 'type' => $type );
				$url = $this->build_url('/search', $params);
				return json_decode($this->get($url),true);
				// return $url;
			}
		}

		public function create_folder($foldername,$parent)
		{
			$params = array('name' => $foldername, 'parent' => array('id'=>$parent));
			$url = $this->build_url('/folders');
			$result = json_decode($this->post($url, json_encode($params)));
			return $result;
	
		}

		public function get_folder_files($folder_id)
		{
			$url = $this->build_url('/folders/'.$folder_id.'/items');
			return json_decode($this->get($url),true);
		}

		public function get_files($file_id)
		{
			$url = $this->build_url('/files/'.$file_id);
			return json_decode($this->get($url),true);
		}

		public function get_folders($folder_id) 
		{
			$url = $this->build_url("/folders/".$folder_id);
			return json_decode($this->get($url),true);
		}

		public function allFiles($folder_path)
		{
			if($this->isLoggedIn())
			{
				$print = array();
				$content = array('items'=>array());

				if($folder_path == "My_Drive")
					$search = $this->search_file('Fuse Storage','folder');
				else
					$search = $this->search_file($folder_path,'folder');
				
				if(!empty($search['entries']))
				{
					$list = $this->get_folder_files($search['entries'][0]['id']);
					foreach ($list['entries'] as $files) {
						if($files['type'] == 'folder')
						{
							$file = $this->get_folders($files['id']);
							$print['file_type'] = 'Folder';
							$print['file_size'] = ' ';
						}
						else
						{
							$file = $this->get_files($files['id']);
							$print['file_type'] = $this->fileType($file['name']).' File';
							$print['file_size'] = $this->formatBytes($file['size']);
						}
						$print['file_id'] = $file['id'];
			            $print['file_name'] = $file['name'];
			            
			            
			            $print['file_modified'] = date("F j, Y, g:i a",strtotime($file['modified_at'])+28800);
			            $print['location'] = 'Box';
			            $content['parent_id'] = $file['parent']['id'];
			            array_push($content['items'], $print);
						
					}
					
				}
				$content['parent_id'] = $search['entries'][0]['id'];
				return $content;
			}

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

		/* Uploads a file */
		public function upload_file($filename, $parent_id) {
			$url = $this->upload_url . '/files/content';
			//$params = array('filename' =>  $filename, 'parent' => array('id'=>$parent_id));
			$filepath = $filename->getClientOriginalName();
			$destinationPath = public_path()."/uploads/box/";
			$filename->move($destinationPath, $filepath);
			$filetobeuploaded = $destinationPath.$filepath;
			$accessToken = $this->access_token;
			$ch = curl_init();
			$params = array('filename'=>'@'.$filetobeuploaded, 'parent_id' => $parent_id);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken"));
			$result=curl_exec($ch);
			curl_close($ch);
			return $result;
		}

		//Download a file
		public function download_file($file_id,$name)
		{
			$url = $this->build_url("/files/".$file_id."/content");
			$content = File::get('https://api.box.com/2.0/files/28781029948/content');
			/*$ch = curl_init();
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$accessToken));
		    curl_setopt($ch, CURLOPT_URL, $url);

		    $jsonData = json_decode(curl_exec($ch));
		    curl_close($ch);*/
			//$return = json_decode($this->get($url), true);

			/*$f = fopen('Downloads/'.Session::get('user')->user_id.'/'.$name, 'wb');
			fwrite($f, $content);
			fclose($f);*/
			dd($content );
			//return $url;
		}
		
		
		/* Deletes a folder */
		public function delete_folder($folder_id) 
		{
			$url = $this->build_url("/folders/".$folder_id);
			$return = json_decode($this->delete($url), true);
			
			return $return;
		}

		/* Deletes a file */
		public function delete_file($file_id) 
		{
			$url = $this->build_url("/files/".$file_id);
			$return = json_decode($this->delete($url),true);
			return $return;
		}
		
		/*
		// Saves the token 
		public function write_token($token, $type = 'file') {
			$array = json_decode($token, true);
			if(isset($array['error'])){
				$this->error = $array['error_description'];
				return false;
			} else {
				$array['timestamp'] = time();
				if($type == 'file'){
					$fp = fopen('token.box', 'w');
					fwrite($fp, json_encode($array));
					fclose($fp);
				}
				return true;
			}
		}
		
		//Reads the token
		public function read_token($type = 'file', $json = false) {
			if($type == 'file' && file_exists('token.box')){
				$fp = fopen('token.box', 'r');
				$content = fread($fp, filesize('token.box'));
				fclose($fp);
			} else {
				return false;
			}
			if($json){
				return $content;
			} else {
				return json_decode($content, true);
			}
		}
		
		// Loads the token
		public function load_token() {
			$array = $this->read_token('file');
			if(!$array){
				return false;
			} else {
				if(isset($array['error'])){
					$this->error = $array['error_description'];
					return false;
				} elseif($this->expired($array['expires_in'], $array['timestamp'])){
					$this->refresh_token = $array['refresh_token'];
					$token = $this->get_token(NULL, true);
					if($this->write_token($token, 'file')){
						$array = json_decode($token, true);
						$this->refresh_token = $array['refresh_token'];
						$this->access_token = $array['access_token'];
						return true;
					}
				} else {
					$this->refresh_token = $array['refresh_token'];
					$this->access_token = $array['access_token'];
					return true;
				}
			}
		}*/
		
		/* Builds the URL for the call */
		private function build_url($api_func, array $opts = array()) {
			$opts = $this->set_opts($opts);
			$base = $this->api_url . $api_func . '?';
			$query_string = http_build_query($opts);
			$base = $base . $query_string;
			return $base;
		}
		
		/* Sets the required before biulding the query */
		private function set_opts(array $opts) {
			if(!array_key_exists('access_token', $opts)) {
				$opts['access_token'] = $this->access_token;
			}
			return $opts;
		}
		
		private function parse_result($res) {
			$xml = simplexml_load_string($res);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			return $array;
		}
		
		private static function expired($expires_in, $timestamp) {
			$ctimestamp = time();
			if(($ctimestamp - $timestamp) >= $expires_in){
				return true;
			} else {
				return false;
			}
		}
		
		private static function get($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		private static function post($url, $params) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		private static function put($url, array $params = array()) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		private static function delete($url, $params = '') {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
	}