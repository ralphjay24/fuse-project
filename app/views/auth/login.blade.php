<!DOCTYPE html>
<html>
<head>
    <title>Fuse Register</title>
</head>
<body>
        {{ HTML::style('auth/css/auth.css')}}
        {{ HTML::script('auth/js/auth.js')}}

<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

<div class="wrapper">
  <h1>Login to Fuse Account</h1>
  <span class="memo">Create an account<a href="{{url('register')}}"> here</a></span>
  {{Form::open(array('url' => 'login', 'method'=>'POST'))}}
    
    @if(Session::has('global'))
      <span>{{ Session::get('global') }}</span>
    @endif

   <input type="text" name="username" placeholder="Username"{{ (Input::old('username')) ? ' value="'. e(Input::old('username')) .'"' : ''}}>
     @if($errors->has('username'))
        {{ $errors->first('username') }}
    @endif

    <input type="password" name="password" placeholder="Password">
     @if($errors->has('password'))
     {{ $errors->first('password') }}
    @endif

    {{ Form::submit('Login') }}
     {{Form::token()}}
 {{Form::close()}}
</div>


<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
  <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

</body>
</html>