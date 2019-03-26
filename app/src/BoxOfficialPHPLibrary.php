
<?php 
set_time_limit(0);
	error_reporting(E_ERROR);
	
	class Box_API {
		
		public $client_id 		= 'uxkpfhefrayagazqa09xsv3jcd5afy7r';
		public $client_secret 	= 'FIH7AQva70FFg6ns9WEIxCaEFzgHsFQn';
		public $redirect_uri	= 'http://localhost:8000/box';
		public $access_token	= '';
		public $refresh_token	= '';
		public $authorize_url 	= 'https://www.box.com/api/oauth2/authorize';
		public $token_url	 	= 'https://www.box.com/api/oauth2/token';
		public $api_url 		= 'https://api.box.com/2.0';
		public $upload_url 		= 'https://upload.box.com/api/2.0';
		


//Authentication , Tokens *************************************************************************
		/* First step for authentication [Gets the code] */
		public function get_code() {
			if(array_key_exists('refresh_token', $_REQUEST)) {
				$this->refresh_token = $_REQUEST['refresh_token'];
			} else {
				$url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
				header('location: ' . $url);
				exit();
			}
		}
		
		/* Second step for authentication [Gets the access_token and the refresh_token] */
		public function get_token($code = '', $json = false){
			$url = $this->token_url;
			if(!empty($this->refresh_token)){
				$params = array('grant_type' => 'refresh_token', 'refresh_token' => $this->refresh_token, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
			} else {
				$params = array('grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
			}
			if($json){
				return $this->post($url, $params);
			}else{
				return json_decode($this->post($url, $params), true);
			}
		}

		/* Saves the token */
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
		
		/* Reads the token */
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
		
		/* Loads the token */
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
		}

		/* check time limit of the token */
		private static function expired($expires_in, $timestamp) {
			$ctimestamp = time();
			if(($ctimestamp - $timestamp) >= $expires_in){
				return true;
			} else {
				return false;
			}
		}



//File and Folder Functions*************************************************************************

	
		/* Gets the current user details */
		public function get_user() {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			Session::put('accessToken',$opts['access_token']);
			
			$base = $this->api_url . "/users/me" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;

			return json_decode($this->get($url),true);
		}
		
		/* Get the details of the mentioned folder */
		public function get_folder_details($folder) {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			//$opts = $this->set_opts($opts);
			$base = $this->api_url . "/folders/$folder" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;

			return json_decode($this->get($url),true);		
		}
		

		/* Get the list of items in the mentioned folder */
		public function get_folder_items($folder) {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			$base = $this->api_url . "/folders/$folder/items" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;

			return json_decode($this->get($url),true);

		}


		public function get_parent_folder($folder) {

			$data = $this->get_folder_details($folder);
			foreach($data['parent'] as $item){
				$array = '';
				$array = $item;
				$return[] = $array;
			}
			return $return;
		}

		
		/* Lists the folders in the mentioned folder */
		public function get_folders($folder) {

			$data = $this->get_folder_items($folder);
			foreach($data['entries'] as $item){
				$array = '';
				if($item['type'] == 'folder'){
					$array = $item;
				}
				$return[] = $array;
			}
			return array_filter($return);
		}
		

		/* Lists the files in the mentioned folder */
		public function get_files($folder) {

			$data = $this->get_folder_items($folder);
			foreach($data['entries'] as $item){
				$array = '';
				if($item['type'] == 'file'){
					$array = $item;
				}
				$return[] = $array;
			}
			return array_filter($return);
		}
		
		
		public function create_folder($name, $parent_id) {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			$base = $this->api_url . "/folders" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;
			$params = array('name' => $name, 'parent' => array('id' => $parent_id));

			return json_decode($this->post($url, json_encode($params)), true);
		}
		

		/* Get the details of the mentioned file */
		public function get_file_details($file) {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			$base = $this->api_url . "/files/$file" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;		

			return json_decode($this->get($url),true);
		}
	

		/* Deletes a folder */
		public function delete_folder($folder) {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			$base = $this->api_url . "/folders/".$folder. '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;

			$return = json_decode($this->delete($url), true);
		}

		
		/* Deletes a file */
		public function delete_file($file) {

			$opts = array();
			$opts['access_token'] = $this->access_token;

			$base = $this->api_url . "/files/$file" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;

			$return = json_decode($this->delete($url),true);
		}


		/* Uploads a file */
		public function upload_file($file,$parent_id) {

			$filepath = $file;
			$accessToken = $this->access_token;
			$ch = curl_init();
			$params = array('filename'=>'@'.$filepath, 'parent_id' => $parent_id);
			 curl_setopt($ch, CURLOPT_URL, $this->upload_url."/files/content");
			 curl_setopt($ch, CURLOPT_POST,1);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			 curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken"));
			 $result=curl_exec($ch);
			 curl_close($ch);
			 var_dump($result);
		}
			

		/* Download a file */
		public function download_file($file,$json=false) {
			$opts = array();
			$opts['access_token'] = $this->access_token;

			$base = $this->api_url . "/files/".$file."/content" . '?';
			$query_string = http_build_query($opts);
			$url = $base . $query_string;

			var_dump($url);
			
		}
		

// Custom URL Methods****************************************************************************************		
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

// Others *****************************************************************************************
		function formatSizeUnits($bytes){
	        if ($bytes >= 1073741824)
	        {
	            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
	        }
	        elseif ($bytes >= 1048576)
	        {
	            $bytes = number_format($bytes / 1048576, 2) . ' MB';
	        }
	        elseif ($bytes >= 1024)
	        {
	            $bytes = number_format($bytes / 1024, 2) . ' KB';
	        }
	        elseif ($bytes > 1)
	        {
	            $bytes = $bytes . ' bytes';
	        }
	        elseif ($bytes == 1)
	        {
	            $bytes = $bytes . ' byte';
	        }
	        else
	        {
	            $bytes = '0 bytes';
	        }

	        return $bytes;
		}	
	}