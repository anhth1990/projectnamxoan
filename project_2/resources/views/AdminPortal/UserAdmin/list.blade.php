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
                <div class="form-group col-lg-4">
                    <label for="">Tên đăng nhập</label>
                    <input class="form-control" id="" name="username" placeholder="Tên đăng nhập" value="{{$searchForm->getUsername()}}">
                </div>
                <div class="form-group col-lg-4">
                    <label for="">Email</label>
                    <input class="form-control" id="" name="email" placeholder="Email" value="{{$searchForm->getEmail()}}">
                </div>
                <div class="form-group col-lg-4">
                    <label for="">Mobile</label>
                    <input class="form-control" id="" name="mobile" placeholder="Số điện thoại" value="{{$searchForm->getMobile()}}">
                </div>
                <div class="form-group col-lg-4">
                    <label for="">Quyền</label>
                    <select class="form-control" name="role">
                        <option value="">Tất cả</option>
                        <option value="ADMIN" @if($searchForm->getRole()=='ADMIN')selected @endif >ADMIN</option>
                        <option value="SUPER_ADMIN" @if($searchForm->getRole()=='SUPER_ADMIN')selected @endif >SUPER_ADMIN</option>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <label for="">Trạng thái</label>
                    <select class="form-control" name="status">
                        <option value="">Tất cả</option>
                        <option value="ACTIVE" @if($searchForm->getStatus()=='ACTIVE')selected @endif >Hoạt động</option>
                        <option value="INACTIVE" @if($searchForm->getStatus()=='INACTIVE')selected @endif >Chưa hoạt động</option>
                    </select>
                </div>
                
                <div class="form-group col-xs-12">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/user-admin/deleteSearch')}}" class="btn btn-default">Xóa tìm kiếm</a>
                </div>
            </div><!-- /.box-body -->

        </form>
        <!-- end form -->

    </div>


    <div class="box" id="t-data">
        <div class="box-header">
            <h3 class="box-title">Danh sách</h3>
            <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/user-admin/add')}}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Tạo quản trị</a>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Tên đăng nhập</th>
                        <th>Thông tin</th>
                        <th>Quyền</th>
                        <th>Trạng thái</th>
                        <th >&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="list-data">
                    @if(isset($listObj) && count($listObj)>0)
                    @foreach($listObj as $key=>$obj)
                    <tr>
                        <td style="width: 10px">{{($page-1)*env('PAGE_SIZE')+intval($key)+1}}</td>
                        <td>{{$obj->username}}</td>
                        <td>
                            <i class="fa fa-user"></i>&nbsp;&nbsp;{{$obj->firstName}} {{$obj->lastName}}<br>
                            <i class="fa fa-mobile-phone"></i>&nbsp;&nbsp;&nbsp; {{$obj->mobile}}<br>
                        <i class="fa fa-envelope-o"></i> {{$obj->email}}<br></td>
                        <td>{{$obj->role}}</td>
                        <td>
                            @if($obj->status == 'ACTIVE')
                            <span class="badge bg-aqua-active">{{trans('common.COMMON_STATUS_'.$obj->status)}}</span>
                          @elseif($obj->status == 'INACTIVE')
                            <span class="badge bg-gray">{{trans('common.COMMON_STATUS_'.$obj->status)}}</span>
                          @endif
                        </td>
                        <td >
                            <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/user-admin/edit/'.$obj->hashcode)}}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i>Chỉnh sửa</a>
                            <a  class="btn btn-xs btn-primary" onclick="refreshPassword('{{$obj->hashcode}}')"><i class="fa fa-refresh"></i>Làm mới mật khẩu</a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                
            </table>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.3/src/loadingoverlay.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.3/extras/loadingoverlay_progress/loadingoverlay_progress.min.js"></script>
<script type="text/javascript">
    function refreshPassword($hashcode){
        $conf = confirm('Bạn có tiếp tục làm mới mật khẩu ?');
        if($conf){
            $.ajax({
                type: "POST",
                url: "{{Asset('/stripe/user-admin/refresh-password')}}",
                data: "hashcode="+$hashcode,
                cache: false,
                beforeSend: function () {
                    $("#t-data").LoadingOverlay("show");
                },
                success: function(data)
                {
                    $response = jQuery.parseJSON(data);
                    alert($response.errMess);
                    $("#t-data").LoadingOverlay("hide");
                }
            });
        }
    }
</script>
@stop