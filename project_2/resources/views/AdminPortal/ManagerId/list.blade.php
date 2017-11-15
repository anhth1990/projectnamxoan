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

    <div class="box">

        <div class="box-header">
            <h3 class="box-title">Tìm kiếm</h3>
        </div><!-- /.box-header -->

        <!-- form start -->
        <form role="form" class="row" action="" method="POST">
            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
            <div class="box-body">
                <div class="form-group col-lg-6">
                    <label for="">ID</label>
                    <input class="form-control" id="" name="identity" placeholder="ID" value="{{$searchForm->getIndentity()}}">
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Tên</label>
                    <input class="form-control" id="" name="name" placeholder="Tên" value="{{$searchForm->getName()}}">
                </div>
                <div class="form-group col-lg-12">
                    <label>
                        <input type="checkbox" name="orderName"  value="1" @if($searchForm->getOrderName()==1) checked @endif > Sắp xếp theo tên
                      </label>
                </div>
                <div class="form-group col-lg-12">
                    <label>
                        <input type="checkbox" name="orderIDOnline" value="1" @if($searchForm->getOrderIDOnline()==1) checked @endif > ID Online
                      </label>
                </div>
                
                <div class="form-group col-xs-12">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/deleteSearch')}}" class="btn btn-default">Xóa tìm kiếm</a>
                </div>
            </div><!-- /.box-body -->

        </form>
        <!-- end form -->

    </div>


    <div class="box" id="t-data">
        
    </div>
    
    


</section><!-- /.content --><!-- /.content -->
@stop

@section('ajaxbox')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.3/src/loadingoverlay.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.3/extras/loadingoverlay_progress/loadingoverlay_progress.min.js"></script>
<script type="text/javascript">
    function reFresh() {
      //window.open(location.reload(true))
      
    }
    
    
    
    
    $(document).ready(function(){
        $(window).load(function() {
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/manager-id/get-data')}}",
                data: "page="+{{$page}},
                cache: false,
                beforeSend: function (xhr) {
                    //$("#t-data").LoadingOverlay("show");
                },
                success: function(data)
                {
                    $('#t-data').html(data);
                    //$("#t-data").LoadingOverlay("hide");
                }
            });
        });
        setInterval(function(){
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/manager-id/get-data')}}",
                data: "page="+{{$page}},
                cache: false,
                beforeSend: function (xhr) {
                    //$("#t-data").LoadingOverlay("show");
                },
                success: function(data)
                {
                    $('#t-data').html(data);
                    //$("#t-data").LoadingOverlay("hide");
                }
            });
        },20000);
        
        $(".deleteAction").on('click',function(){
            
        })
        
    })
    
    function deleteAction(ex){
        $conf = confirm('Bạn có tiếp tục xóa ?');
        $hashcode = ex.attr('data-id');
        $i = ex;
        if($conf){
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/manager-id/delete')}}",
                data: "hashcode="+$hashcode,
                cache: false,
                //beforeSend: function (xhr) {
                //    App.blockUI({
                //        target: '#blockui_sample_1_portlet_body'
                //    });
                //},
                success: function(data)
                {
                    $response = jQuery.parseJSON(data);
                    //alert($response.errMess);
                    if($response.errCode==200){
                        $i.parent().parent().hide('slow');
                    }else{
                        alert($response.errMess);
                    }

                }
            });
        }
        
    }
    
    function deleteInfo(){
        $conf = confirm('Bạn có tiếp tục xóa hết phần thông tin ?');
        if($conf){
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/manager-id/delete-all')}}",
                cache: false,
                success: function(data)
                {
                    $response = jQuery.parseJSON(data);
                    if($response.errCode==200){
                        location.reload();
                    }else{
                        alert($response.errMess);
                    }

                }
            });
        }
    }
</script>
@stop