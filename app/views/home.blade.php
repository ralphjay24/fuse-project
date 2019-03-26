<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>Fuse UCA</title>
		<link rel="shortcut icon" href="../favicon.ico">
	    
	    {{ HTML::style('css/bootstrap.min.css')}}
	    {{ HTML::style('css/reset.css')}}
	    {{ HTML::style('css/cod_style.css')}}

	    {{ HTML::style('css/normalize.css')}}
	    {{ HTML::style('css/demo.css')}}
	    {{ HTML::style('css/component.css')}}
	    {{ HTML::style('http://fonts.googleapis.com/css?family=PT+Sans:400,700')}}


	    {{ HTML::script('js/modernizr.js') }}
   		{{ HTML::script('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js') }}
   		{{ HTML::script('https://oss.maxcdn.com/respond/1.4.2/respond.min.js') }}
		
	</head>
	<body>
		<div class="wrapper demo-1">
			<div class="content">
				<div id="large-header" class="large-header">
					<canvas id="demo-canvas"></canvas>
					<!-- <h1 class="main-title">FUSE <span class="thin">UCA</span></h1> -->
				</div>

				<div class="codrops-header main-nav">
					<h1>One app for all your Clouds <span>Multiple Clouds + 23GB free memory + Realtime</span></h1>
					<nav class="codrops-demos">
						<a class="current-demo cd-signin" href="{{url('login')}}">Sign In</a>
						<a class="cd-signup" href="{{url('register')}}">Sign Up</a>
					</nav>
				</div> <!-- codrops header -->

				

			</div>
			<!-- Related demos -->
			<section class="related">
				<p>Fuse United Cloud Storage (UCS) is powered by:</p>
				<a href="http://tympanus.net/Development/HeaderEffects/">
					<img src="img/g.jpg" />
				</a>
				<a href="http://tympanus.net/Development/ArticleIntroEffects/">
					<img src="img/d.jpg" />
				</a>
				<a href="http://tympanus.net/Development/ArticleIntroEffects/">
					<img src="img/b.jpg" />
				</a>
			</section>
			<p class="ref">Coded by Team Fuse: <a href="http://www.eso.org/images/eso1205a/">Pepito and Tigue. 2015 All Rights Reserved</a></p>
		</div><!-- /CONTENT -->
		
    	{{ HTML::script('https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js')}}
    	{{ HTML::script('bootstrap/js/bootstrap.min.js')}}
    	{{ HTML::script('js/main.js')}}

    	{{ HTML::script('js/TweenLite.min.js')}}
    	{{ HTML::script('js/EasePack.min.js')}}
    	{{ HTML::script('js/rAF.js')}}
    	{{ HTML::script('js/demo-1.js')}}
		
	</body>
</html>