<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Manager</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{Asset('public/Admin/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{Asset('public/Admin/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="{{Asset('public/Admin/dist/css/skins/_all-skins.min.css')}}" rel="stylesheet" type="text/css" />
    
    <!-- Select 2-->
    <link href="{{Asset('public/Admin/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    
    <!-- custom css -->
    <link href="{{Asset('public/Admin/dist/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="skin-blue">
    <div class="wrapper">
      
        <!-- HEADER -->
        @include("AdminPortal.Layouts.header")
        <!-- END HEADER -->
      
        <!-- SLIDE BAR -->
        @yield('slidebar')
        <!-- END SLIDE BAR -->
        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->
      
        <!-- FOOTER -->
        @include("AdminPortal.Layouts.footer")
        <!-- END FOOTER -->
      
    </div><!-- ./wrapper -->



    <div class="modal" id="myModal">
        <div class="modal-dialog" >
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
              <h4 class="modal-title">Modal Default</h4>
            </div>
            <div class="modal-body">
              <p>One fine body…</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div>


    <!-- jQuery 2.1.3 -->
    <script src="{{Asset('public/Admin/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{Asset('public/Admin/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="{{Asset('public/Admin/plugins/slimScroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='{{Asset('public/Admin/plugins/fastclick/fastclick.min.js')}}'></script>
    <!-- AdminLTE App -->
    <script src="{{Asset('public/Admin/dist/js/app.min.js')}}" type="text/javascript"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{Asset('public/Admin/dist/js/demo.js')}}" type="text/javascript"></script>
    <!-- Select 2 -->
    <script src="{{Asset('public/Admin/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
    
    @yield('ajaxbox')
  </body>
</html>
