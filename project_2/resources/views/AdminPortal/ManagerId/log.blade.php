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
                        <th style="width: 10px"><a  style="cursor: pointer" class="fa fa-trash" onclick="deleteMultiAction()"></a></th>
                        <th style="width: 10px">#</th>
                        <th>ID</th>
                        <th>Ip</th>
                        <th>Thời gian</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="list-data">
                    @if(isset($listObj) && count($listObj)>0)
                    @foreach($listObj as $key=>$obj)
                    <tr >
                        <td><input type="checkbox" name="chkbox[]"  value="{{$obj->hashcode}}" /></td>
                        <td>{{($page-1)*env('PAGE_SIZE')+intval($key)+1}}</td>
                        <td>{{$obj->identity->identity}}</td>
                        <td>{{$obj->ip}} </td>
                        <td><?php echo date_format(date_create($obj->time_log),"d/m/Y H:i:s"); ?> </td>
                        <td> <a  style="cursor: pointer" class="fa fa-trash" onclick="deleteAction($(this))" data-id="{{$obj->hashcode}}">Xóa</a></td>
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
<script type="text/javascript">
    function deleteAction(ex){
        $conf = confirm('Bạn có tiếp tục xóa ?');
        $hashcode = ex.attr('data-id');
        $i = ex;
        if($conf){
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/manager-id/log/delete')}}",
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
    
    function deleteMultiAction(){
        $conf = confirm('Bạn có tiếp tục xóa ?');
        var myCheckboxes = new Array();
        $("input:checked").each(function() {
           myCheckboxes.push($(this).val());
        });
        if($conf){
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/manager-id/log/deleteMulti')}}",
                data: "myCheckboxes="+myCheckboxes,
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
                        //$i.parent().parent().hide('slow');
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