<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Yupax</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{Asset('public/Admin/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{Asset('public/Admin/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="{{Asset('public/Admin/plugins/iCheck/square/blue.css')}}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="/"><b>Admin</b>Portal</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">{{trans('common.login_admin_page')}}</p>
        @include("AdminPortal.Blocks.error")
        @include("AdminPortal.Blocks.success")
        <form action="" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="{{trans('common.username')}}" name="username" value="{{$userAdminForm->getUsername()}}" autocomplete="off" />
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="{{trans('common.password')}}" name="password" autocomplete="off" />
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">    
              <div class="checkbox icheck">
                <label>
                    <input type="checkbox" name="saveLogin"> {{trans('common.save_status_login')}}
                </label>
              </div>                        
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">{{trans('common.sign_in')}}</button>
            </div><!-- /.col -->
          </div>
        </form>
        <!--
        <div class="social-auth-links text-center">
          <p>- OR -</p>
          <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
          <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
        </div>
        -->
        
        <a href="javascript:void(0)" onclick="alert('Updating...')" >{{trans('common.forgot_password')}}</a><br>
        <a href="javascript:void(0)" onclick="alert('Updating...')" class="text-center">{{trans('common.sign_up')}}</a>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="{{Asset('public/Admin/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{Asset('public/Admin/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="{{Asset('public/Admin/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>