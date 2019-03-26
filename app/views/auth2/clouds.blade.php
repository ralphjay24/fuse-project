<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
	<title>Sync Clouds</title>
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="auth/css/default.css" />
		<link rel="stylesheet" type="text/css" href="auth/css/component.css" />

		<link rel="stylesheet" type="text/css" href="auth/css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="auth/css/vicons-font.css" />
		<link rel="stylesheet" type="text/css" href="auth/css/base.css" />
		<link rel="stylesheet" type="text/css" href="auth/css/buttons.css" />

		<script src="js/modernizr.custom.js"></script>
	</head>
<style>
header {
    text-align: justify;
    background: #2c3e50;
    color: #fff;
}

nav li {
    display: inline-block;
    margin: 10px;
}

.active{
	border-bottom: solid 5px #8AC007;
}

nav{
	padding: 40px;
}

.button--moema{
	background: #3498db;
}

#sync{
	background: #27ae60;
}

</style>
<body>
<nav><b>Choose the Drives you want to sync..</b></nav>
	<div class="container">
			<div class="main">
            {{Form::open(array('class'=>'cbp-mc-form'))}}
				
				
				<div class="cbp-mc-column">
					<h1>Sync Google Drive</h1>
					<p>Note: By syncing Google, you get <b>13 GB</b> memory.</p>
					<section class="content">
						<img src="auth/img/icons/google.png">
						<div class="box">
							@if(!$storage['google'])
			                <a href="{{url('google')}}">{{Form::button('Sync Now',array('class'=>'button button--moema button--border-thick button--size-s'))}}</a>
			              @else
			                {{Form::button('Synced',array('class'=>'button button--moema button--border-thick button--size-s', 'id'=>'sync'))}}
			              @endif
			              </div>
					</section>
					<a href="{{url('success')}}">
					{{Form::button('IM DONE',array('class'=>'button button--ujarak button--border-thin button--text-thick'))}}
					</a>
					</div>

	  				<div class="cbp-mc-column">
					<h1>Sync Box</h1>
					<p>Note: By syncing Box, you get <b>8 GB</b> memory.</p>
					<section class="content">
						<img src="auth/img/icons/box2.png">
						<div class="box">
							@if(!$storage['box'])
			                <a href="{{url('box-signin')}}">{{Form::button('Sync Now',array('class'=>'button button--moema button--border-thick button--size-s'))}}</a>
			              @else
			                {{Form::button('Synced',array('class'=>'button button--moema button--border-thick button--size-s', 'id'=>'sync'))}}
			              @endif
			              </div>
					</section>


				</div>

				<div class="cbp-mc-column">
					<h1>Sync Dropbox</h1>
					<p>Note: By syncing Dropbox, you get <b>2 GB</b> memory.</p>
					<section class="content">
						<img src="auth/img/icons/dropbox.png">
						<div class="box">
							@if(!$storage['dropbox'])
			                <a href="{{url('dropbox')}}">{{Form::button('Sync Now',array('class'=>'button button--moema button--border-thick button--size-s'))}}</a>
			              @else
			                {{Form::button('Synced',array('class'=>'button button--moema button--border-thick button--size-s', 'id'=>'sync'))}}
			              @endif
			              </div>
					</section>
	  			</div>

			</div>
		</div>
	</body>
</html>
