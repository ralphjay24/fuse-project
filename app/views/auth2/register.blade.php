<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
	<title>Register Fuse</title>
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
            {{Form::open(array('url' => 'register', 'method'=>'POST', 'class'=>'cbp-mc-form'))}}
			
				<div class="cbp-mc-column">
				<h4>PERSONAL INFORMATION</h4>
				<p>@if(Session::has('global'))
                  <span>{{ Session::get('global') }}</span>
                @endif</p>

				<label for="firstname">First Name</label>
					<input type="text" name="firstname" placeholder="Juan">
					
					<label for="lastname">Last Name</label>
					<input type="text" name="lastname" placeholder="Dela Cruz">

					<label for="email">Email</label>
					<input type="text"  name="email" placeholder="juan_delacruz@email.com">
				</div>

				<div class="cbp-mc-column">
				<h4>ACCOUNT INFORMATION</h4>

				<label for="username">Username</label>
					<input type="text"  name="username" placeholder="juandelacruz">
				
					<label for="password">Password</label>
						<input type="password" name="password" placeholder="********">
					
					<label for="password_confirm">Confirm Password</label>
						<input type="password" name="password_confirm" placeholder="********">
					</div>

					<div class="cbp-mc-column">
						<div class="cbp-mc-submit-wrap"><br><br><br><br><br><br><br><br><br><br>
	  				 	{{Form::submit('Next >',array('class'=>'btn cbp-mc-submit'))}}
	  					</div>
					</div>
	  				
  				
  				{{Form::token()}}
          {{Form::close()}}

		</div>
	</body>
</html>
