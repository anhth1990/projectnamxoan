@extends('AdminPortal.Layouts.home-default')
<!-- include slidebar -->
@section('slidebar')
@include('AdminPortal.Layouts.slidebar',[
'nav'=>'4',
'sub'=>'1'
])
@stop
<!-- end include slidebar -->
@section('content')
<!-- Content Header (Page header) -->
<!-- include navigator -->
@include('AdminPortal.Layouts.navigator',[
'titleModule'=>trans('portal/common.user_admin'),
'titleModuledetail'=>'',
'navModule'=>trans('portal/common.user_admin'),
'linkModule'=>'/'.env("PREFIX_ADMIN_PORTAL").'/user-admin',
'navActive'=>trans('common.add'),
])
<!-- end include navigator -->
<!-- Main content -->
<section class="content">

    <div class="box">

        <div class="box-header">
            <h3 class="box-title">Thêm mới</h3>
        </div><!-- /.box-header -->
        <div class="progress">
            <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%">

            </div>
        </div>
        @if(isset($error))
        <div class="callout callout-danger">
            <p>{{$error}} </p>
        </div>
        @endif
        <!-- form start -->
        <form role="form" class="row" method="POST" action="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/user-admin/add-confirm')}}">
            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
            <div class="box-body">
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Tên đăng nhập *</label>
                    <input class="form-control" id="" placeholder="Tên đăng nhập" name="username"  value="{{$addForm->getUsername()}}">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Mật khẩu *</label>
                    <input type="password" class="form-control" id="" placeholder="Mật khẩu" name="password"  value="">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Xác nhận mật khẩu *</label>
                    <input type="password" class="form-control" id="" placeholder="Xác nhận mật khẩu" name="rePassword"  value="">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Họ</label>
                    <input class="form-control" id="" placeholder="Họ" name="firstName" value="{{$addForm->getFirstName()}}">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Tên</label>
                    <input class="form-control" id="" placeholder="Tên" name="lastName" value="{{$addForm->getLastName()}}">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Email</label>
                    <input class="form-control" id="" placeholder="Email" name="email" value="{{$addForm->getEmail()}}">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Số điện thoại</label>
                    <input class="form-control" id="" placeholder="Số điện thoại" name="mobile" value="{{$addForm->getMobile()}}">
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Quyền</label>
                    <select class="form-control" name="role">
                        <option value="ADMIN" @if($addForm->getRole()=='ADMIN')checked @endif>ADMIN</option>
                        <option value="SUPER_ADMIN" @if($addForm->getRole()=='SUPER_ADMIN')checked @endif >SUPER ADMIN</option>
                    </select>
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Trạng thái</label>
                    <select class="form-control" name="status">
                        <option value="ACTIVE" @if($addForm->getStatus()=='ACTIVE')checked @endif >Hoạt động</option>
                        <option value="INACTIVE" @if($addForm->getStatus()=='INACTIVE')checked @endif >Chưa hoạt động</option>
                    </select>
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <button type="submit" class="btn btn-primary">Xác nhận</button>
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