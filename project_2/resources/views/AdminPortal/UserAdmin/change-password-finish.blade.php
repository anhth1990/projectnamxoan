@extends('AdminPortal.Layouts.home-default')
<!-- include slidebar -->
@section('slidebar')
@include('AdminPortal.Layouts.slidebar',[
'nav'=>'0',
'sub'=>'0'
])
@stop
<!-- end include slidebar -->
@section('content')
<!-- Content Header (Page header) -->
<!-- include navigator -->
@include('AdminPortal.Layouts.navigator',[
'titleModule'=>'Thay đổi mật khẩu',
'titleModuledetail'=>'',
'navModule'=>'Tổng quan',
'linkModule'=>'/'.env("PREFIX_ADMIN_PORTAL"),
'navActive'=>'Thay đổi mật khẩu',
])
<!-- end include navigator -->
<!-- Main content -->
<section class="content">

    <div class="box">

        <div class="box-header">
            <h3 class="box-title">Thay đổi mật khẩu</h3>
        </div><!-- /.box-header -->
        <div class="progress">
            <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">

            </div>
        </div>
        <div class="callout callout-success">
            <p>Thành công </p>
        </div>
        <!-- form start -->
        <!-- end form -->

    </div>


</section><!-- /.content --><!-- /.content -->
@stop

@section('ajaxbox')

@stop