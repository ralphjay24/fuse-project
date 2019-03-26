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
  <h1>Register For An Account</h1>
  <span class="memo">Already a member?  Click<a href="{{url('login')}}"> here</a></span>

  {{Form::open(array('url' => 'register', 'method'=>'POST'))}}
    <input type="text" name="email" placeholder="Email" {{ (Input::old('email')) ? ' value="'. e(Input::old('email')) .'"' : ''}}>
    @if($errors->has('email'))
        {{ $errors->first('email') }}
    @endif
    
    <input type="text" name="username" placeholder="Username"{{ (Input::old('username')) ? ' value="'. e(Input::old('username')) .'"' : ''}}>
     @if($errors->has('username'))
        {{ $errors->first('username') }}
    @endif
     
    <input type="password" name="password" placeholder="Password">
     @if($errors->has('password'))
        {{ $errors->first('password') }}
    @endif

    <input type="password" name="password_confirm" placeholder="Confirm Password">
     @if($errors->has('password_confirm'))
        {{ $errors->first('password_confirm') }}
    @endif

    <br> <br> Which cloud storage you want to use first? <br>
    {{ Form::label('google', 'Google') }}
    {{ Form::checkbox('google', 'true') }}

    {{ Form::label('dropbox', 'Dropbox') }}
    {{ Form::checkbox('dropbox', 'true') }}

    {{ Form::label('box', 'Box') }}
    {{ Form::checkbox('box', 'true') }}
    <br> <br> 
    {{Form::submit('Create Account')}}
    {{Form::token()}}
 {{Form::close()}}
</div>

</body>
</html>