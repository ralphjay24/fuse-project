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
      .login-box{margin-top:20px; padding: 5px;}
      label { font-size: 12px; color: red;}

      
    </style>
</head>


<body>
    <div class="container-fluid-full">
    <div class="row-fluid">
          
      <div class="row-fluid">
        <div class="login-box">
          <div class="icons">
            <a href="{{url('login')}}">already have an account?</a>
          </div>
          <h2 style="font-family:Roboto,Century;">Register to Fuse</h2>
          
            <fieldset>
               <label><p>@if(Session::has('global'))
                  <span>{{ Session::get('global') }}</span>
                @endif</p></label>
              {{Form::open(array('url' => 'register', 'method'=>'POST', 'class'=>'form-horizontal'))}}

              <div class="input-prepend">
              <span class="add-on"><i class="halflings-icon user"></i></span>
                <input class="input-large span10" name="firstname" type="text" placeholder="First name"/>
                <br>
                <label>
                 @if($errors->has('firstname'))
                    {{ $errors->first('firstname') }}
                @endif</label>
                
                <span class="add-on"><i class="halflings-icon user"></i></span>
                <input class="input-large span10" name="lastname" type="text" placeholder="Last name"/>
                <br>
                 <label>@if($errors->has('lastname'))
                    {{ $errors->first('lastname') }}
                @endif</label>

                <span class="add-on"><i class="halflings-icon user"></i></span>
                <input class="input-large span10" name="email" type="text" placeholder="Email"{{ (Input::old('email')) ? ' value="'. e(Input::old('email')) .'"' : ''}}>
                <br>
                 <label>@if($errors->has('email'))
                    {{ $errors->first('email') }}
                @endif</label>


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

                 <span class="add-on"><i class="halflings-icon lock"></i></span>
                <input class="input-large span10" name="password_confirm" type="password" placeholder="Confirm password"/>
               <label>@if($errors->has('password_confirm'))
                    {{ $errors->first('password_confirm') }}
                @endif</label>
              </div>
              

             
                {{Form::submit('Next',array('class'=>'btn btn-block btn-primary'))}}
              
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