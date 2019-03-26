
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>
   Laravel
  </title>
  <!--<link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
  <script src="http://codeorigin.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js"></script>
  <script src="http://sydcanem.com/bootstrap-contextmenu/bootstrap-contextmenu.js"></script>-->

  
 </head>
 <body>
 
  {{ Form::open(array('url'=>'/upload/0B4tLfw51R3THT0ZRQWoyLU96bmc','files'=>true)) }}
  
  {{ Form::label('file','',array('id'=>'','class'=>'')) }}
  {{ Form::file('files','',array('id'=>'','class'=>'')) }}
  <br/>
  <!-- submit buttons -->
  {{ Form::submit('Save') }}
  
  <!-- reset buttons -->
  {{ Form::reset('Reset') }}
  
  {{ Form::close() }}
  @if(Session::has('token'))
    {{ link_to('/logout','Logout')}}
  @endif
  

 </body>
</html>