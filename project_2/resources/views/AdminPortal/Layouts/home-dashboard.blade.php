<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{$titlePage}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{Asset('public/Admin/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />    
    <!-- FontAwesome 4.3.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{Asset('public/Admin/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="{{Asset('public/Admin/dist/css/skins/_all-skins.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="{{Asset('public/Admin/plugins/iCheck/flat/blue.css')}}" rel="stylesheet" type="text/css" />
    <!-- Morris chart -->
    <link href="{{Asset('public/Admin/plugins/morris/morris.css')}}" rel="stylesheet" type="text/css" />
    <!-- jvectormap -->
    <!--
    <link href="plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
  -->
    <!-- Date Picker -->
    <link href="{{Asset('public/Admin/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
    <!-- Daterange picker -->
    <!--
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
  -->
    <!-- bootstrap wysihtml5 - text editor -->
    <!--
    <link href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
    -->

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

    <!-- jQuery 2.1.3 -->
    <script src="{{Asset('public/Admin/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>
    <!-- jQuery UI 1.11.2 -->
    <script src="{{Asset('public/Admin/plugins/jQuery/jquery-ui.min.js')}}" type="text/javascript"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{Asset('public/Admin/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>    
    <!-- Morris.js charts -->
    <!--
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="{{Asset('public/Admin/plugins/morris/morris.min.js')}}" type="text/javascript"></script>
-->
    <!-- Sparkline -->
    <!--
    <script src="plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
    -->
    <!-- jvectormap -->
    <!--
    <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
    <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
  -->
    <!-- jQuery Knob Chart -->
    <!--
    <script src="plugins/knob/jquery.knob.js" type="text/javascript"></script>
  -->
    <!-- daterangepicker -->
    <!--
    <script src="plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
  -->
    <!-- datepicker -->
    <script src="{{Asset('public/Admin/plugins/datepicker/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{Asset('public/Admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="{{Asset('public/Admin/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="{{Asset('public/Admin/plugins/slimScroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
    <!-- FastClick -->
    <!--
    <script src='plugins/fastclick/fastclick.min.js'></script>
  -->
    <!-- AdminLTE App -->
    <script src="{{Asset('public/Admin/dist/js/app.js')}}" type="text/javascript"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{Asset('public/Admin/dist/js/pages/dashboard.js')}}" type="text/javascript"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="{{Asset('public/Admin/dist/js/demo.js')}}" type="text/javascript"></script>
    @yield('ajaxbox')
  </body>
</html>