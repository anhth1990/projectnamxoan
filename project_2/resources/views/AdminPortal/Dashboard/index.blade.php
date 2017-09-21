@extends('AdminPortal.Layouts.home-dashboard')
<!-- include slidebar -->
@section('slidebar')
@include('AdminPortal.Layouts.slidebar',[
    'nav'=>'1',
    'sub'=>'1'
])
@stop
<!-- end include slidebar -->
@section('content')
<!-- Content Header (Page header) -->
<!-- include navigator -->
@include('AdminPortal.Layouts.navigator',[
    'titleModule'=>trans('common.dashboard'),
    'titleModuledetail'=>'',
    'navModule'=>'',
    'linkModule'=>'',
    'navActive'=>trans('dashboard.title_page'),
])
<!-- end include navigator -->

<!-- Main content -->
<section class="content">

  <!-- Thông kê chung -->
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{$totalId}}</h3>
          <p>Số ID</p>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
        
      </div>
    </div><!-- ./col -->
    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{$totalIdOnline}}</h3>
          <p>Số ID Online</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        
      </div>
    </div><!-- ./col -->
    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{$totalIdOffline}}</h3>
          <p>Số ID Offline</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
      </div>
    </div><!-- ./col -->
  </div><!-- /.row -->
  <!-- End Thông kê chung -->

 

</section><!-- /.content -->
@stop
@section('ajaxbox')
<script type="text/javascript">
    function reFresh() {
      window.open(location.reload(true))
    }
    window.setInterval("reFresh()",20000);
    
    
</script>
@stop