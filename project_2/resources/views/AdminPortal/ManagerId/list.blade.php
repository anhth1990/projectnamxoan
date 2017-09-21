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
                
                <div class="form-group col-xs-12">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/deleteSearch')}}" class="btn btn-default">Xóa tìm kiếm</a>
                </div>
            </div><!-- /.box-body -->

        </form>
        <!-- end form -->

    </div>


    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Danh sách</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <tbody><tr>
                        <th style="width: 10px">#</th>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Thông tin</th>
                        <th>Trạng thái</th>
                        <th >&nbsp;</th>
                    </tr>
                    @if(isset($listObj) && count($listObj)>0)
                    @foreach($listObj as $key=>$obj)
                    <tr >
                        <td>{{($page-1)*env('PAGE_SIZE')+intval($key)+1}}</td>
                        <td>{{$obj->identity}}</td>
                        <td style="width: 150px">{{$obj->name}} </td>
                        <td>
                            <table class="table table-bordered">
                                @foreach($obj->identityDetail as $objDetail)
                                <tr>
                                    <td>
                                        <i class="fa fa-clock-o"></i>&nbsp;&nbsp; {{$objDetail->time}} <br>
                                        <i class="fa fa-link"></i>&nbsp;&nbsp; {{$objDetail->url}} <br>
                                        <i class="fa fa-barcode"></i>&nbsp;&nbsp; {{$objDetail->code}} <br>
                                    </td>
                                    <td>
                                        @if($objDetail->type == 0)
                                            <span class="badge bg-blue">An toàn</span>
                                          @elseif($obj->status == 1)
                                            <span class="badge bg-red">Nguy hiểm</span>
                                          @endif
                                        
                                    </td>
                                    <td style="width: 50px">
                                        <a  style="cursor: pointer" class="fa fa-trash deleteAction" data-id="{{$objDetail->hashcode}}">Xóa</a>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                        <td><?php //echo date_format($obj->updated_at,"d/m/Y H:i:s"); ?>
                            
                            @if(time()-strtotime($obj->last_login)< env('TIME_OFFLINE'))
                                <span class="badge bg-green">Online</span>
                            @else
                                <span class="badge bg-info">Offline</span>
                            @endif
                        </td>
                        <td style="width: 100px"><a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/edit/'.$obj->hashcode)}}" class="fa fa-edit" style="cursor: pointer">Chỉnh sửa</a></td>
                    </tr>
                    @endforeach
                    @endif
                </tbody></table>
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
            @if(isset($listObj) && count($listObj)>0)
                        <?php echo $listObj->render(); ?>
                      @endif
        </div>
    </div>


</section><!-- /.content --><!-- /.content -->
@stop

@section('ajaxbox')
<script type="text/javascript">
    function reFresh() {
      window.open(location.reload(true))
    }
    window.setInterval("reFresh()",20000);
    
    $(document).ready(function(){
        $(".deleteAction").on('click',function(){
            $conf = confirm('Bạn có tiếp tục xóa ?');
            $hashcode = $(this).attr('data-id');
            $i = $(this);
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
        })
    })
</script>
@stop