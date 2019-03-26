<!DOCTYPE html>
<html>
<head>
  <title>Fuse Registration</title>

  {{HTML::style("countdown/css/style.css")}}
  {{HTML::style("countdown/css/style-responsive.css")}}
  {{HTML::style("countdown/css/bootstrap-responsive.min.css")}}
  {{HTML::style("countdown/css/bootstrap.min.css")}}
  <style type="text/css">
      body { background: url(countdown/img/bg-login.jpg) !important; }
      .login-box{margin-top:20px;padding: 5px;}
      label { font-size: 12px; color: red;}
    </style>
</head>


<body>
    <div class="container-fluid-full">
    <div class="row-fluid">
          
      <div class="row-fluid">
        <div class="login-box">
          <div class="icons">
            or <a href="{{url('register')}}">create an account</a>
          </div>
          <h2 style="font-family:Roboto,Century;">Login to Fuse</h2>
          
            <fieldset>
                <label><p>@if(Session::has('global'))
                  <span>{{ Session::get('global') }}</span>
                @endif</p></label>

              {{Form::open(array('url' => 'login', 'method'=>'POST', 'class'=>'form-horizontal'))}}

              <div class="input-prepend">
             
                <span class="add-on"><i class="halflings-icon ok"></i></span>
                <input class="input-large span10" name="username" type="text" placeholder="Username"{{ (Input::old('username')) ? ' value="'. e(Input::old('username')) .'"' : ''}}>
                <br>
                 <label>@if($errors->has('username'))
                    {{ $errors->first('username') }}
                @endif</label>

                
                <span class="add-on"><i class="halflings-icon lock"></i></span>
                <input class="input-large span10" name="password" type="password" placeholder="Password"/>
                <br>
                 <label>@if($errors->has('password'))
                    {{ $errors->first('password') }}
                @endif</label>
              </div>

             {{Form::submit('Login',array('class'=>'btn btn-block btn-primary'))}}
             
          {{Form::token()}}
          {{Form::close()}}
          </fieldset>
         
        </div><!--/span-->
      </div><!--/row-->
      

  </div><!--/.fluid-container-->
  
    </div><!--/fluid-row-->
  


    
 {{HTML::script("countdown/js/jquery-1.9.1.min.js")}}
  {{HTML::script("countdown/js/modernizr.js")}}
  {{HTML::script("countdown/js/bootstrap.min.js")}}

</body>
</html>