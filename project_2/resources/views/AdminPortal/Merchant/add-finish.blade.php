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
    'navModule'=>trans('common.list'),
    'linkModule'=>'/'.env("PREFIX_ADMIN_PORTAL").'/merchant/list',
    'navActive'=>trans('common.add'),
])
<!-- end include navigator -->
<!-- Main content -->
<section class="content">

  <div class="box">
      
      <div class="pad margin no-print">
        <div class="callout callout-info" style="margin-bottom: 0!important;">                        
          <h4><i class="fa fa-info"></i> {{trans('common.add-finish')}}</h4>
          
        </div>
      </div>

    <!-- error -->
    @include("AdminPortal.Blocks.error")
    <!-- end error -->

    <!-- form start -->
      <div class="box-body">
        
        <table class="table table-bordered">
            <tbody>
            <tr>
              <th>{{trans('merchant.first_name')}}</th>
              <td>{{$merchantForm->getFirstName()}}</td>
            </tr>
            <tr>
              <th>{{trans('merchant.last_name')}}</th>
              <td>{{$merchantForm->getLastName()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.images')}}</th>
              <td><img src="{{Asset($merchantForm->getImages())}}" height="60px"  ></td>
            </tr>
            <tr>
              <th>{{trans('merchant.name_merchant')}}</th>
              <td>{{$merchantForm->getName()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.email')}}</th>
              <td>{{$merchantForm->getEmail()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.mobile')}}</th>
              <td>{{$merchantForm->getMobile()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.city')}}</th>
              <td>{{$merchantForm->getProvinceName()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.district')}}</th>
              <td>{{$merchantForm->getDistrictName()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.ward')}}</th>
              <td>{{$merchantForm->getWardName()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.address')}}</th>
              <td>{{$merchantForm->getAddress()}}</td>
            </tr>
            <tr>
              <th>{{trans('common.status')}}</th>
              <td>{{trans('common.status_'.$merchantForm->getStatus())}}</td>
            </tr>
          </tbody>
        </table>
        
          
      </div><!-- /.box-body -->
      
      <div class="box-footer clearfix">
          <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/merchant/list')}}" class="btn btn-default">{{trans('common.list')}}</a>       
        </div>
      
      
    <!-- end form -->

  </div>


</section><!-- /.content -->
<script type="text/javascript">
    
    
    
</script>
@stop
