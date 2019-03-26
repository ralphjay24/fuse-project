<!DOCTYPE html>
<html lang="en">
<head>
  
  <!-- start: Meta -->
  <meta charset="utf-8">
  <title>Fuse</title>
  {{HTML::style("countdown/css/bootstrap-responsive.min.css")}}
  {{HTML::style("countdown/css/bootstrap.min.css")}}
  {{HTML::style("countdown/css/style.css")}}
  {{HTML::style("countdown/css/style-responsive.css")}}
  {{HTML::style("countdown/css/pace.css")}}


  <style type="text/css">
    body{
      background: #7f8c8d;
    }

    .navbar .nav > li > .dropdown-menu:before,
    .navbar .nav > li > .dropdown-menu:after {
        display: none;
    }

    .show{
      display: block;
    }

    .span6, .span12{
      display: none !important;
    }

    .modal {
      color:black;
      text-shadow:none;
    }

    .breadcrumb a{
      color: black;
    }

    .navbar-inner{
      background: #3BABF5 !important;
      border-bottom: none;
    }

    #auto{
      position:relative;
      overflow:hidden;
    }

    .spinner {
    position: fixed;
    top: 50%;
    left: 50%;
    margin-left: -50px; /* half width of the spinner gif */
    margin-top: -50px; /* half height of the spinner gif */
    text-align:center;
    z-index:1234;
    overflow: auto;
    width: 100px; /* width of the spinner gif */
    height: 102px;
  }

  footer{
    background: #7f8c8d;
  }

  #sidebar-left{
    background: #7f8c8d;
  }

</style>
  </head>

<body>
  <div class="navbar">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="{{ url('/') }}"><div style="width:160; height:30;">{{HTML::image('img/banner.png', 'Fuse', array('style' => 'width:125px; height:40px;'))}}</div></a>
                
        <!-- start: Header Menu -->
        <div class="nav-no-collapse header-nav">
          <ul class="nav pull-right">


          
          <li><a style="color:white;text-shadow:none;"><span>Memory: {{$files['quota']['used']}} / {{$files['quota']['total']}}</span></a></li>


            <li class="dropdown thumb-dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="{{url('home/My_Drive')}}">
                <i class="halflings-icon white user"></i>&nbsp;&nbsp;Hi,{{ Session::get('user')->firstname}}
              </a>
               <ul class="dropdown-menu" role="menu">
               <li><a data-toggle="modal" data-target="#outModal"> Logout</a></li>

               </ul>

              
            </li>


          </ul>
        </div>

        <!-- end: Header Menu -->

      </div>
    </div>
  </div>


 <!-- MODALS -->
<div id="deleteModal" class="modal fade">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Confirm</h4>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete these files?
                  </div>
                  <div class="modal-footer">
                      {{ Form::button('Delete', array('id'=>'deleteBtn','class'=>'btn btn-block btn-danger')) }}
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div id="downloadModal" class="modal fade">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Download File</h4>
                  </div>
                  <div class="modal-body">
                    Click to confirm download..
                  </div>
                  <div class="modal-footer">
                      {{ Form::button('Download', array('id'=>'downloadBtn','class'=>'btn btn-block btn-success')) }}
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


          <div id="newFolderModal" class="modal fade">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create Folder</h4>
                  </div>
                  {{ Form::open(array('url'=>'createFolder')) }}
                  <div class="modal-body">
                    <div class="form-group">
                      
                      {{ Form::hidden('parent[google]', $files['gparent_id'])}}
                      {{ Form::hidden('parent[dropbox]', $files['dparent_id'])}}
                      {{ Form::hidden('parent[box]', $files['bparent_id'])}}
                     
                      {{ Form::hidden('redirect', Request::url())}}
                    {{ Form::text('foldername',null,array('placeholder'=>'NewFolder'))}}
                      
                    </div>
                  </div>
                  <div class="modal-footer">
                      {{ Form::submit('Create', array('id'=>'newFolderBtn','class'=>'btn btn-block btn-primary')) }}
                    {{ Form::close() }}
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!-- end of new folder modal -->

             <div id="outModal" class="modal fade">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Confirm</h4>
                  </div>
                  {{ Form::open(array('url'=>'/logout', 'method'=>'get')) }}
                  <div class="modal-body">
                    Are you sure you want to log out?
                  </div>
                  <div class="modal-footer">
                      {{ Form::submit('Logout', array('id'=>'logoutBtn','class'=>'btn btn-block btn-primary')) }}
                   {{ Form::close() }}
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

           
           
           <div id="uploadModal" class="modal fade">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Upload files</h4>
                  </div>
                  {{ Form::open(array('url'=>'/upload','files'=>true)) }}
                  <div class="modal-body">

                      {{ Form::hidden('gparent', $files['gparent_id'])}}
                      {{ Form::hidden('dparent', $files['dparent_id'])}}
                      {{ Form::hidden('bparent', $files['bparent_id'])}}
                      
                      {{ Form::hidden('redirect', Request::url())}}
                      {{ Form::label('files', 'Note: Maximum upload is 100MB.');}}
                      {{ Form::file('files[]',array('multiple'=>'true')) }}
                      @if($errors->has('files'))
                        {{ $errors->first('files') }}
                      @endif
                  </div>
                  <div class="modal-footer">
                      {{ Form::submit('Upload', array('id'=>'uploadBtn','class'=>'btn btn-block btn-primary')) }}
                  {{Form::close()}}
                    </div>
                  
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


            
    <!-- END MODALS -->

  <!-- start: Header -->
  
    <div class="container-fluid-full">
    <div class="row-fluid">
        
      <!-- start: Main Menu -->
      <div id="sidebar-left" class="span2">
        <div class="nav-collapse sidebar-nav ">
          <ul class="nav nav-tabs nav-stacked main-menu">

          <li data-toggle="collapse" data-target="#products" class="collapsed">
                  <a><i class="halflings-icon white home"></i> Clouds <span class="arrow"></span></a>
                </li>
                <ul class="sub-menu collapse" id="products">
              <li>
              @if(Session::has('gtoken'))
              <a style="background:#27ae60;"><i class="halflings-icon ok-sign"></i>  Google Drive</a>
                
            @else
               
              <a href={{ url('add/google') }}><i class="halflings-icon remove-sign"></i>  Google Drive</a>

            @endif
            </li>
              <li>
             

              @if(Session::has('dtoken'))
              <a style="background:#27ae60;"><i class="halflings-icon ok-sign"></i>  Dropbox</a>
            
          
            @else
               
              <a href={{ url('add/dropbox') }}><i class="halflings-icon remove-sign"></i>  Dropbox</a>
            @endif
            </li>
              <li>
            

            @if(Session::has('btoken'))
                <a style="background:#27ae60;"><i class="halflings-icon ok-sign"></i>  Box</a>
                
            @else
               
              <a href={{ url('add/box') }}><i class="halflings-icon remove-sign"></i>  Box</a>

            @endif
            </li>
              <li>
              </ul>

            <li><a data-toggle="modal" data-target="#downloadModal"><i class="halflings-icon white download"></i><span class="hidden-tablet"> Download</span></a></li>
            <li><a data-toggle="modal" data-target="#uploadModal"><i class="halflings-icon white upload"></i><span class="hidden-tablet"> Upload</span></a></li>
            <li><a data-toggle="modal" data-target="#deleteModal"><i class="halflings-icon white trash"></i><span class="hidden-tablet"> Delete</span></a></li>
            <li><a data-toggle="modal" data-target="#newFolderModal"><i class="halflings-icon white folder-open"></i><span class="hidden-tablet"> Create Directory</span></a></li>
            </ul>
        </div>
      </div>
      <!-- end: Main Menu -->

    
      <div id="content" class="span10">
      <ul class="breadcrumb">
        <li>
          <a id="refreshBtn" class="btn btn-default"><i class="icon-cog"></i>Refresh</a>
          </li>

      
         <div id="spinner" class="spinner" style="display:block;">
         <!-- {{ HTML::image('img/spinner.gif', 'loading', array('id' => 'img-spinner')) }} -->
         <i class="fa fa-spinner fa-pulse"></i>
      </div>

      </ul>

     

      <div class="row-fluid sortable">
       @if($request == 'success')
        <div class="alert alert-success" role="alert">Successfully Downloaded!</div>
        @elseif($request == 'failed')
        <div class="alert alert-danger" role="alert">Download failed! Something went wrong.</div>
        @endif

        <div class="box">
          <div class="box-header" data-original-title>
            <h2><i class="halflings-icon eye-open"></i><span class="break"></span>My Files</h2>
          </div>
          <div class="box-content">
            <table id="auto" class="table table-hover table-striped table-bordered bootstrap-datatable datatable">
              <thead>
              @if(!empty($files['folders']) || !empty($files['files']))
              <tr>
                  <th> </th>
                 <th>Name</th>
                  <th>Type</th>
                  <th>Date Modified</th>
                  <th>Size</th>
                  
                </tr>
              </thead>   
              <tbody >
                {{ Form::open(array('url'=>'evaluate','check_list'=>true, 'method'=>'POST', 'id'=>'check_list')) }}
                <!-- {{ $folders = array() }} -->
                <?php $folders = array(); ?>

                @foreach($files['folders'] as $file)
                    <?php if (!in_array($file['file_name'], $folders)): ?>
                      <?php array_push($folders, $file['file_name']) ?>
                      <tr>
                        <td><input type="checkbox" name="check_list[]" value="{{$file['file_id']}}"></td>
                        <td>
                          <a href={{url("/home/".$file['file_name'])}}>
                            {{ $file['file_name'] }}
                          </a>
                        </td>
                        <td class="center">{{ $file['file_type'] }}</td>
                        <td class="center">{{ $file['file_modified'] }}</td>
                        <td></td>
                        <td></td>
                      </tr>
                    <?php endif ?>
                  @endforeach

                @foreach($files['files'] as $file)
                    <tr>
                    <td><input type="checkbox" name="check_list[]" value="{{$file['file_id']}}"></td>
                    
                   <td>{{ $file['file_name'] }}</td>
                    <td class="center">{{ $file['file_type'] }}</td>
                    <td class="center">{{ $file['file_modified'] }}</td>
                    <td>{{ $file['file_size'] }}</td>
                    
                    </tr>
                  @endforeach
                  {{Form::hidden('action')}}
                  {{ Form::close()}}
              
              @elseif(!empty($message))
                  <tr>
                      <td colspan="6" class="alert alert-success" role="alert"><b>{{ $message }}</b></td>
                  </tr>
              @endif 
                
              </tbody>
            </table>            
          </div>
        </div><!--/span-->
      
      </div><!--/row-->

      
        

  </div><!--/.fluid-container-->
  
      <!-- end: Content -->
    </div><!--/#content.span10-->
    </div><!--/fluid-row-->
    
  
  <div class="clearfix"></div>
  
  <footer>

    <p>
      <span>Team Fuse/Fuze : RJ Pepito & KJ Tigue</span>
    </p>

  </footer>
  
  <!-- start: JavaScript-->
  {{HTML::script("countdown/js/jquery-1.9.1.min.js")}}
  {{HTML::script("countdown/js/modernizr.js")}}
  {{HTML::script("countdown/js/bootstrap.min.js")}}
  {{HTML::script("countdown/js/pace.js")}}

  <script type="text/javascript">

        $(document).ready(function(){
          
         $('.btn-block').click(function() {
            $('.spinner').show().delay(5000).fadeOut();
            });

          //  $('#uploadBtn').click(function() {
          //     $('#uploadModal').modal('hide');
          //     $('.spinner').show();
          //     $('#auto').fadeIn('slow');

          // });

          //  $('#newFolderBtn').click(function() {
          //     $('#newFolderModal').modal('hide');
          //     $('.spinner').show();
          //     $('#auto').fadeIn('slow');
          // });

          //   $('#logoutBtn').click(function() {
          //     $('#outModal').modal('hide');
          //     $('.spinner').show();
          // });


            $('#deleteBtn').click(function(){
              $('input[name=action]').val("delete");
              $('#check_list').submit();
            });

            $('#downloadBtn').click(function(){
              $('input[name=action]').val("download");
              $('#check_list').submit();
            });

             $('#refreshBtn').click(function() {
              //$('#auto').load(location.href+ ' #auto').fadeIn("slow");
              location.reload();
             }); 

          });




    </script>
  
</body>
</html>