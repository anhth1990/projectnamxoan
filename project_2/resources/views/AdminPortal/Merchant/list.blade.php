@extends('AdminPortal.Layouts.home-default')
<!-- include slidebar -->
@section('slidebar')
@include('AdminPortal.Layouts.slidebar',[
    'nav'=>'2',
    'sub'=>'1'
])
@stop
<!-- end include slidebar -->
@section('content')
<!-- Content Header (Page header) -->
<!-- include navigator -->
@include('AdminPortal.Layouts.navigator',[
    'titleModule'=>trans('merchant.title'),
    'titleModuledetail'=>'',
    'navModule'=>trans('merchant.title'),
    'linkModule'=>'/'.env("PREFIX_ADMIN_PORTAL").'/merchant/list',
    'navActive'=>trans('common.list'),
])
<!-- end include navigator -->
<!-- Main content -->
<section class="content">

<div class="box">
  <div class="box-header">
        <h3 class="box-title">{{trans('common.list')}}</h3>
      </div><!-- /.box-header -->
      <form role="form" method="post" class="row">
          <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
    <div class="box-body">
      <div class="form-group col-lg-4">
        <label for="">{{trans('merchant.name_merchant')}}</label>
        <input type="text" class="form-control" id="" placeholder="{{trans('merchant.name_merchant')}}" name="name" value="{{$searchForm->getName()}}">
      </div>
      <div class="form-group col-lg-4">
        <label for="">{{trans('common.email')}}</label>
        <input type="text" class="form-control" id="" placeholder="{{trans('common.email')}}" name="email" value="{{$searchForm->getEmail()}}">
      </div>
      <div class="form-group col-lg-4">
        <label for="">{{trans('common.mobile')}}</label>
        <input type="text" class="form-control" id="" placeholder="{{trans('common.mobile')}}" name="mobile" value="{{$searchForm->getMobile()}}">
      </div>
      <div class="form-group col-lg-4">
        <label for="exampleInputEmail1">{{trans('common.status')}}</label>
        <select class="form-control __web-inspector-hide-shortcut__" name="status">
            <option value="">{{trans('common.all')}}</option>
            @foreach($listStatusUser as $status)
            <option value="{{$status}}" 
                    @if ($searchForm->getStatus() ==$status)selected @endif
                    >{{trans('common.status_'.$status)}}</option>
            @endforeach
        </select>
      </div>
      <div class="form-group col-xs-12">
        <button type="submit" class="btn btn-primary">Thống kê</button>
        <a class="btn btn-default" href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/merchant/deleteSearch')}}">Xóa tìm kiếm</a>
      </div>
    </div><!-- /.box-body -->

  </form>
  <div class="box-footer">
    Có {{$countMerchantDao}} bản ghi được tìm thấy
  </div>
</div>


<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <a class="btn btn-primary" href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/merchant/add')}}">{{trans('common.add')}}</a>
      </div><!-- /.box-header -->
      <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
          <tr>
            <th>No</th>
            <th>{{trans('merchant.name_merchant')}}</th>
            <th>{{trans('common.email')}}</th>
            <th>{{trans('common.mobile')}}</th>
            <th>{{trans('common.status')}}</th>
            <th>&nbsp;</th>
          </tr>
          @if(isset($listMerchantDao) && count($listMerchantDao)>0)
          @foreach($listMerchantDao as $key=>$merchant)
          <tr>
            <td>{{($page-1)*env('PAGE_SIZE')+intval($key)+1}}</td>
            <td>{{$merchant->name}}</td>
            <td>{{$merchant->email}}</td>
            <td>{{$merchant->mobile}}</td>
            <td>
                @if($merchant->status == 'ACTIVE')
                <span class="label label-success">{{trans('common.status_'.$merchant->status)}}</span>
                @endif
                @if($merchant->status == 'INACTIVE')
                <span class="label label-warning">{{trans('common.status_'.$merchant->status)}}</span>
                @endif
            </td>
            <td>
              <a href="#" title="Chỉnh sửa"><i class="fa fa-edit "></i></a>
              <a href="#" title="Xóa"><i class="fa fa-trash-o "></i></a>
            </td>
          </tr>
          @endforeach
          @endif
        </table>
      </div><!-- /.box-body -->
      <div class="box-footer clearfix">
        @if(isset($listMerchantDao) && count($listMerchantDao)>0)
          <?php echo $listMerchantDao->render(); ?>
        @endif
      </div>
    </div><!-- /.box -->




  </div>
</div>
</section><!-- /.content --><!-- /.content -->
@stop