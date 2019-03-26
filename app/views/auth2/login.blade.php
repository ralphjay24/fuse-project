<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
	<title>Login Fuse</title>
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="auth/css/component.css" />
		<link rel="stylesheet" type="text/css" href="auth/css/default.css" />
		<script src="js/modernizr.custom.js"></script>
	</head>
<style>

nav li {
    display: inline-block;
    margin: 10px;
}


nav{
	padding: 5px 20px;
	background: #cfd8dc;
}

p{ font-size: 16px; color: rgb(213, 51, 51);}


</style>

	<body>
	
		<header>
		<nav><img src="img/banner.png" style="width:150px;"></nav>
		</header>
			<div class="container">
			<div class="main">
            {{Form::open(array('url' => 'login', 'method'=>'POST', 'class'=>'cbp-mc-form'))}}
			<div class="cbp-mc-column">
			</div>
            
            <div class="cbp-mc-column">

            <p>@if(Session::has('global'))
                  <span>{{ Session::get('global') }}</span>
                @endif</p>

					<label for="username">Username</label>
					<input type="text"  name="username" placeholder="Username">
				
					<label for="password">Password</label>
						<input type="password" name="password" placeholder="********">
					</div>
	  				
  				<div class="cbp-mc-submit-wrap">
  				 {{Form::submit('Login',array('class'=>'btn cbp-mc-submit'))}}
  				 </div>
  				 {{Form::token()}}
          {{Form::close()}}
			</div>
		</div>
	</body>
</html>
