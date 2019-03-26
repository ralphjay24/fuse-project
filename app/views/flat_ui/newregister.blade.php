<!DOCTYPE html>
<html>
<head>
	<title></title>
{{ HTML::style('flat/dist/css/vendor/bootstrap.min.css')}}
 {{ HTML::style('flat/dist/css/flat-ui.css')}}
  {{ HTML::style('flat/mycss/heloworld.css')}}
 
  
</head>
<body>
	
<div class="login-screen" id="register">
          <div class="login-form" >

            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="Email" id="email" />
              <label class="login-field-icon fui-mail" for="login-name"></label>
            </div>


            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="Username" id="username" />
              <label class="login-field-icon fui-user" for="login-name"></label>
            </div>

            <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Password" id="password" />
              <label class="login-field-icon fui-lock" for="login-pass"></label>
            </div>

             <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Confirm Password" id="password_confirm" />
              <label class="login-field-icon fui-lock" for="login-pass"></label>
            </div>

            <div class="share mrl">
            Which cloud storage you want to use? 
                <label class="checkbox" for="checkbox1">
            <input type="checkbox" value="" id="checkbox1" data-toggle="checkbox">
            Google
          </label>

          <label class="checkbox" for="checkbox2">
            <input type="checkbox" value="" id="checkbox2" data-toggle="checkbox">
            Dropbox
          </label>

          <label class="checkbox" for="checkbox3">
            <input type="checkbox" value="" id="checkbox3" data-toggle="checkbox">
            Box
          </label>
                </div>

            <a class="btn btn-primary btn-lg btn-block" href="#">Register</a>
            <a class="login-link" href="{{url('newlogin')}}">I already have an account</a>
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