<!DOCTYPE html>
<html>
<head>
	<title></title>
  
</head>
<body>
	{{ HTML::style('flat/dist/css/vendor/bootstrap.min.css')}}
	{{ HTML::style('flat/dist/css/flat-ui.css')}}
	<!-- {{ HTML::style('flat/docs/assets/css/demo.css')}} -->
	{{ HTML::style('flat/mycss/heloworld.css')}}
	
	<div class="login-screen">
          <!-- <div class="login-icon">
            <img src="img/login/icon.png" alt="Welcome to Mail App" />
            <h4>Welcome to <small>Fuse UCA</small></h4>
          </div> -->

          <div class="login-form">
            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="Enter your username" id="login-name" />
              <label class="login-field-icon fui-user" for="login-name"></label>
            </div>

            <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Password" id="login-pass" />
              <label class="login-field-icon fui-lock" for="login-pass"></label>
            </div>

            <a class="btn btn-primary btn-lg btn-block" href="#">Log in</a>
            <a class="login-link" href="{{url('newregister')}}">Create an account</a>
          </div>
        </div>
    
 {{ HTML::script('flat/dist/js/vendor/jquery.min.js')}}
  {{ HTML::script('flat/dist/js/vendor/video.js')}}
  {{ HTML::script('flat/dist/js/flat-ui.min.js')}}
  {{ HTML::script('flat/docs/assets/js/application.js')}}
  <script>
      videojs.options.flash.swf = "dist/js/vendors/video-js.swf"
  </script>

</body>
</html>