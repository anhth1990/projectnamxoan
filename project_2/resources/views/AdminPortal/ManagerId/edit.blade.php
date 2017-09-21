@extends('AdminPortal.Layouts.home-default')
<!-- include slidebar -->
@section('slidebar')
@include('AdminPortal.Layouts.slidebar',[
'nav'=>'3',
'sub'=>'1'
])
@stop
<!-- end include slidebar -->
@section('content')
<!-- Content Header (Page header) -->
<!-- include navigator -->
@include('AdminPortal.Layouts.navigator',[
'titleModule'=>trans('portal/common.manager'),
'titleModuledetail'=>'',
'navModule'=>trans('portal/common.manager'),
'linkModule'=>'/'.env("PREFIX_ADMIN_PORTAL").'/manager-id',
'navActive'=>trans('common.update'),
])
<!-- end include navigator -->
<!-- Main content -->
<section class="content">

    <div class="box">

        <div class="box-header">
            <h3 class="box-title">Chỉnh sửa : {{$identityForm->getIndentity()}}</h3>
        </div><!-- /.box-header -->

        <!-- form start -->
        <form role="form" class="row" method="POST" action="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/edit')}}">
            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
            <input type="hidden" name="hashcode" value="{{$identityForm->getHashcode()}}">
            <div class="box-body">
                <div class="form-group col-lg-6">
                    <label for="">ID</label>
                    <input class="form-control" id="" placeholder="ID" disabled="" value="{{$identityForm->getIndentity()}}">
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Tên</label>
                    <input class="form-control" id="" placeholder="Tên" name="name" value="{{$identityForm->getName()}}">
                </div>
                
                <div class="form-group col-xs-12">
                    <button type="submit" class="btn btn-primary">Chỉnh sửa</button>
                    <a href="{{Asset('/'.env("PREFIX_ADMIN_PORTAL").'/manager-id')}}" class="btn btn-default" >Quay lại</a>
                </div>
            </div><!-- /.box-body -->

        </form>
        <!-- end form -->

    </div>


</section><!-- /.content --><!-- /.content -->
@stop

@section('ajaxbox')

@stop