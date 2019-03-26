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
              
              <label>Google</label>
              @if(!$storage['google'])
                <a href="{{url('google')}}">{{Form::button('Authenticate Here',array('class'=>'btn btn-block btn-primary'))}}</a>
              @else
                {{Form::button('Authenticated Successfully',array('class'=>'btn btn-block btn-primary'))}}
              @endif
              <br><br>
              <label>Dropbox</label>
              @if(!$storage['dropbox'])
                <a href="{{url('dropbox')}}">{{Form::button('Authenticate Here',array('class'=>'btn btn-block btn-primary'))}}</a>
              @else
                {{Form::button('Authenticated Successfully',array('class'=>'btn btn-block btn-primary'))}}
              @endif
              <br><br>
              <label>Box</label>
              @if(!$storage['box'])
                <a href="{{url('box-signin')}}">{{Form::button('Authenticate Here',array('class'=>'btn btn-block btn-primary'))}}</a>
              @else
                {{Form::button('Authenticated Successfully',array('class'=>'btn btn-block btn-primary'))}}
              @endif

             <br><br><br><br>
              <a href="{{url('success')}}">
                {{Form::button('Register Account',array('class'=>'btn btn-block btn-primary'))}}
              </a>
              
         
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