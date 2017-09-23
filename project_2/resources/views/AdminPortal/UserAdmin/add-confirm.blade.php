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
            <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100" style="width: 66%">

            </div>
        </div>
        <!-- form start -->
        <form role="form" class="row" method="POST" action="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/user-admin/add-confirm')}}">
            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
            <div class="box-body">
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Tên đăng nhập</label>
                    {{$addForm->getUsername()}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Họ</label>
                    {{$addForm->getFirstName()}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Tên</label>
                    {{$addForm->getLastName()}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Email</label>
                    {{$addForm->getEmail()}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Số điện thoại</label>
                    {{$addForm->getMobile()}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Quyền</label>
                    {{$addForm->getRole()}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Trạng thái</label>
                    {{trans('common.COMMON_STATUS_'.$addForm->getStatus())}}
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/user-admin/add-finish')}}" class="btn btn-primary">Thêm mới</a>
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