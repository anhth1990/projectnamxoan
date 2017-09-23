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
            <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">

            </div>
        </div>
        @if(isset($error))
        <div class="callout callout-danger">
            <p>{{$error}} </p>
        </div>
        @endif
        <!-- form start -->
        <form role="form" class="row" method="POST" action="">
            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
            <div class="box-body">
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Mật khẩu hiện tại *</label>
                    <input type="password" class="form-control" id="" placeholder="Mật khẩu hiện tại" name="password"  value="">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Mật khẩu mới *</label>
                    <input type="password" class="form-control" id="" placeholder="Mật khẩu mới" name="newPassword" value="">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Xác nhận mật khẩu mới *</label>
                    <input type="password" class="form-control" id="" placeholder="Xác nhận mật khẩu mới" name="reNewPassword" value="">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <button type="submit" class="btn btn-primary">Thay đổi mật khẩu</button>
                    <button type="button" onclick="history.go(-1)" class="btn btn-default" >Quay lại</button>
                </div>
            </div><!-- /.box-body -->

        </form>
        <!-- end form -->

    </div>


</section><!-- /.content --><!-- /.content -->
@stop

@section('ajaxbox')

@stop