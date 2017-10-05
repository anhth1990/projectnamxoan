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
'navActive'=>trans('common.list'),
])
<!-- end include navigator -->
<!-- Main content -->
<section class="content">

    


    <div class="box" >
        <div class="box-header">
            <h3 class="box-title">Log : {{$obj->identity}} / {{$obj->name}}</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>ID</th>
                        <th>Ip</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody class="list-data">
                    @if(isset($listObj) && count($listObj)>0)
                    @foreach($listObj as $key=>$obj)
                    <tr >
                        <td>{{($page-1)*env('PAGE_SIZE')+intval($key)+1}}</td>
                        <td>{{$obj->identity->identity}}</td>
                        <td>{{$obj->ip}} </td>
                        <td><?php echo date_format(date_create($obj->time_log),"d/m/Y H:i:s"); ?> </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                
            </table>
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
            @if(isset($listObj) && count($listObj)>0)
          <?php  echo $listObj->render(); ?>
        @endif
        </div>
    </div>
    
    


</section><!-- /.content --><!-- /.content -->
@stop

@section('ajaxbox')

@stop