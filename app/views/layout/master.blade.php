<!DOCTYPE html>
<html>
<head>
	<title>FUSE&trade;</title>
	@yield('css')

	<style>
		@section('styles')
			.navbar-header{
				padding: 5px;
			}

			.navbar-brand img{
				position: absolute;
				top:-7px;
				left: -25px;
			}
			
		@show
	</style>

	@yield('js_top')

</head>
<body>

	<div class="container" id="block">
		@yield('content')
	</div>

	@yield('content2')
	
	@yield('js_bottom')

</body>
</html>