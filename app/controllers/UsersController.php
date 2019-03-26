<?php

class UsersController extends \BaseController {

	public function index(){

	}

	public function getLogin(){
		if(Session::has('users'))
			return Redirect::to('home/My_Drive');
		else
			return View::make('auth2.login');
	}

	public function logout()
	{

		DB::table('files')->where('user_id','=',Session::get('user')->user_id)->delete();

		Session::flush();
		return Redirect::to('login');

	}

	public function getRegister(){
		if(Session::has('users'))
			return Redirect::to('home/My_Drive');
		else
			return View::make('auth2.register');
	}

	public function getAuthenticate()
	{
		if(Session::has('users'))
			return Redirect::to('home/My_Drive');
		elseif(Session::has('register'))
		{
			//echo "<pre>";
			//print_r(Session::get('storage'));
			$storage = array('google'=>false,'dropbox'=>false,'box'=>false);
			if(Session::has('storage'))
			{
				if(in_array('google', Session::get('storage')))
					$storage['google'] = true;
				
				if(in_array('dropbox', Session::get('storage')))
					$storage['dropbox'] = true;
				
				if(in_array('box', Session::get('storage')))
					$storage['box'] = true;
			}
			
			return View::make('auth2.clouds')->with('storage',$storage);
		}	
		else
			return Redirect::to('login')->with('global','Restriction url.');
	}

	public function doAuthenticate($storage)
	{
		if(Session::has('register'))
		{
			if($storage == 'google')
				return Redirect::to('/google');
			elseif($storage == 'dropbox')
				return Redirect::to('/dropbox');
			elseif($storage == 'box')
				return Redirect::to('/box-signin');
		}
		else
			return Redirect::to('login')->with('global','Restriction url.');
	}

	public function success_register()
	{
		if(Session::has('register'))
		{
			File::makeDirectory('Downloads/'.Session::get('register')->user_id);
			Upload::create(['user_id' => Session::get('register')->user_id, 'last_session' => json_encode(Session::get('storage'))]);
			Session::flush();
			return Redirect::to('login')->with('global','You have successfully registered.');
		}
		else
			return Redirect::to('login')->with('global','Restriction url.');
	}

	
	public function doRegister(){
		$validator = Validator::make(Input::all(),
			array(
				'email'				=>'required|max:50|email|unique:users',
				'username'			=>'required|max:20|min:3|unique:users',
				'password'			=>'required|min:6',
				'password_confirm'	=>'required|same:password',
				'firstname'			=>'required',
				'lastname'			=>'required',
				)
			);

		if($validator->fails()){
			return Redirect::to('register')->withErrors($validator)->withInput();
		}else{
			//create acct
			
			$user_id 	= str_random(50);
			$email		= Input::get('email');
			$username	= Input::get('username');
			$password 	= Input::get('password');
			$fname 		= ucwords(Input::get('firstname'));
			$lname 		= ucwords(Input::get('lastname'));
			
			$create = User::create(array(
				'user_id' 	=> $user_id,
				'email'	  	=> $email,
				'username'	=> $username,
				'password'	=> Hash::make($password),
				'firstname' => $fname,
				'lastname'	=> $lname
				));


			if($create){
				Session::put('register',User::find($user_id));
				return Redirect::to('auth_register');//->with('global','You have successfully registered.');
			}
		}
	}

	public function isIdExisted($stingId){
		//query db for string id
		//return true or false

	}

	public function doLogin(){
		$validator = Validator::make(Input::all(),
			array(
				'username'			=>'required',
				'password'			=>'required',
				));
		if($validator->fails()){
			return Redirect::to('login')->withErrors($validator)->withInput();
		}else{
			$username	=Input::get('username');
			$password 	=Input::get('password');

			$auth = Auth::attempt(array(
				'username'=>$username,
				'password'=>$password
				));
			$user = DB::table('users')->where('username', $username)->first();
			
			if($auth){
				Session::put('user',$user);
				
				$upload = DB::table('upload')->where('user_id', Session::get('user')->user_id)->first();
	    		Session::put('upload',json_decode($upload->last_session,true));
				return Redirect::to('process');
			}else{
				return Redirect::to('login')->with('global','Something went wrong.');
			}
		}
		
	}

	

}