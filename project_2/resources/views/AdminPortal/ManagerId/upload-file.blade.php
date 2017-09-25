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
            <h3 class="box-title">Tải dữ liệu</h3>
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
        <form role="form" class="row" method="POST" action="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/upload-file')}}" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="box-body">
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">Tải dữ liệu (.xlsx) *</label>
                    <input type="file" name="file" >
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <label for="">File mẫu</label>
                    <a href="{{Asset('/public/uploads/data/test1.xlsx')}}">Tải tại đây</a>
                </div>
                <div class="form-group col-md-offset-3 col-lg-6">
                    <button type="submit" class="btn btn-primary">Tải lên</button>
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