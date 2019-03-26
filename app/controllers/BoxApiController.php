<?php

class BoxApiController extends \BaseController {

	private $box;
	
	public function __construct(Box_Service $box)
	{
		//mana og pass ang credenntials dire
		$this->box = $box;
	}

	public function index()
	{
		
		// var_dump($url);
		return Redirect::to( (string)$url );
	}

	public function login()
	{
		$code = Input::get('code');
		//$state = Input::get('state');
		if(!empty($code)){
			$token =  $this->box->get_token($code);

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
   					 	'btoken'  => $token['refresh_token']
				));
				
        	}
        	else 
        	{
        		DB::table('token')
				->where('user_id', $current->user_id)
				->update(['btoken' => $token['refresh_token']]);
				
				
			}

			DB::table('users')
					->where('user_id', $current->user_id)
					->update(['isBox' => 'accepted']);
			
			if(Session::has('register'))
          	{
            	if(Session::has('storage'))
            	{
            	  $storage = Session::get('storage');
            	  if(!in_array('box', $storage))
	              {
	                array_push($storage, 'box');
	                Session::put('storage',$storage);  
	              }
            	}
            	else
            	{
            		Session::put('storage',array('box'));
            	  	
            	}
            	return Redirect::to('auth_register');
          	}
          	if(Session::has('user'))  
        		return Redirect::to('process');
		}
		else 
		{

			$url = $this->box->get_code();
			if(Session::has('user') || Session::has('register'))
				return Redirect::to( (string)$url );
			else
        		return Redirect::to('login')->with('global','Need to login');
		}
		
		
	}



	public function index1()
	{
		$storage = new Session();
		$code = Input::get( 'code' );
	    $boxService = OAuth::consumer('Box');

		if (!empty($code)) {
			$state = isset($_GET['state']) ? $_GET['state'] : null;

		    $token = $boxService->requestAccessToken($code,$state);
		    Session::put("btoken",$token->getAccessToken());
		    $result = json_decode($boxService->request('/users/me'), true);
		    
		    $files = json_decode($boxService->request('/folders/0?fields=item_collection,name'), true);
		    Session::put('files',$files);
		     
		     return Redirect::to('box_getfile/index3');
		
		} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	 		$url = $boxService->getAuthorizationUri();
		    return Redirect::to( (string)$url );
		} else {
			$url = Request::url() . '?go=go';
		    return Redirect::to($url);
		}
	}

	/*public function delete($fileId)
	{
		if($this->gd->isLoggedIn())
			$this->gd->delete_file($fileId);
	}*/

}