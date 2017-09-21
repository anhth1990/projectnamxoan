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

    <div class="box-header">
      <h3 class="box-title">{{trans('common.add')}}</h3>
    </div><!-- /.box-header -->

    <!-- error -->
    @include("AdminPortal.Blocks.error")
    <!-- end error -->

    <!-- form start -->
    <form  enctype="multipart/form-data" role="form" class="row" action="" method="post" >
        <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
        <input type="hidden" name="lat" id="lat" value="{{$merchantForm->getLat()}}">
        <input type="hidden" name="long" id="long" value="{{$merchantForm->getLong()}}">
      <div class="box-body">
        <div class="form-group col-lg-4">
          <label for="">{{trans('merchant.first_name')}}</label>
          <input type="text" class="form-control" id="" placeholder="{{trans('merchant.first_name')}}" name="firstName" value="{{$merchantForm->getFirstName()}}" >
        </div>
        <div class="form-group col-lg-4">
          <label for="">{{trans('merchant.last_name')}}</label>
          <input type="text" class="form-control" id="" placeholder="{{trans('merchant.last_name')}}" name="lastName" value="{{$merchantForm->getLastName()}}" >
        </div>
        <div class="form-group col-lg-4">
            <label for="">{{trans('common.images')}}</label>
            <input type="file" class="form-control"  id="" name="images" value="">
        </div>
        <div class="form-group col-lg-4">
          <label for="">{{trans('merchant.name_merchant')}}</label>
          <input type="text" class="form-control" id="" placeholder="{{trans('merchant.name_merchant')}}" name="name" value="{{$merchantForm->getName()}}" >
        </div>
        <div class="form-group col-lg-4">
          <label for="">{{trans('common.email')}}</label>
          <input type="text" class="form-control" id="" placeholder="{{trans('common.email')}}" name="email" value="{{$merchantForm->getEmail()}}" >
        </div>
        <div class="form-group col-lg-4">
          <label for="">{{trans('common.mobile')}}</label>
          <input type="text" class="form-control" id="" placeholder="{{trans('common.mobile')}}" name="mobile" value="{{$merchantForm->getMobile()}}" >
        </div>
        <div class="form-group col-lg-4">
            <label for="">{{trans('common.city')}}</label>
            <select class="form-control __web-inspector-hide-shortcut__" name="provinceId" id="provinceId">
            </select>
        </div>
        <div class="form-group col-lg-4">
            <label for="">{{trans('common.district')}}</label>
            <select class="form-control __web-inspector-hide-shortcut__" name="districtId" id="districtId">
                <option value="">{{trans("common.please_choose")}}</option>
            </select>
        </div>
        <div class="form-group col-lg-4">
            <label for="">{{trans('common.ward')}}</label>
            <select class="form-control __web-inspector-hide-shortcut__" name="wardId" id="wardId">
                <option value="">{{trans("common.please_choose")}}</option>
            </select>
        </div>
        <div class="form-group col-lg-6">
          <label for="">{{trans('common.address')}}</label>
          <input type="text" class="form-control" id="address" placeholder="{{trans('common.address')}}" name="address" value="{{$merchantForm->getAddress()}}">
        </div>
        <div class="form-group col-lg-6">
            <label for="">{{trans('common.status')}}</label>
            <select class="form-control __web-inspector-hide-shortcut__" name="status">
                @foreach($listStatusUser as $status)
                <option value="{{$status}}" 
                        @if ($merchantForm->getStatus() ==$status)selected @endif
                        >{{trans('common.status_'.$status)}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-lg-6">
            <div id='map_canvas' style='width:400px; height:500px;'></div>
        </div>
          
        
        <div class="form-group col-xs-12">
          <button type="submit" class="btn btn-primary">{{trans('common.add')}}</button>
          <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/merchant/list')}}" class="btn btn-default">{{trans('common.back')}}</a>
          <!--
          <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>
          -->
        </div>
          
      </div><!-- /.box-body -->

    </form>
    <!-- end form -->

  </div>


</section><!-- /.content -->
<!-- jQuery 2.1.3 -->
<script src="{{Asset('public/Admin/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>
<script type='text/javascript'
src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDFLaJwxTIGpZmwfpbEyOU5XZglUq6-5iM&sensor=false'>
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#provinceId").select2();
        $("#districtId").select2();
        $("#wardId").select2();
        $("body").attr("onload","initialize('','')");
        $("#provinceId").html(getProvince());
        $("#provinceId").on("change",function(){
           $provinceId = $(this).val();
           $("#districtId").html(getDistrict($provinceId));
           $("#wardId").html(getWard(''))
        });
        $("#districtId").on("change",function(){
           $districtId = $(this).val();
           $("#wardId").html(getWard($districtId));
        });
        $("#address").on("blur",function(){
            $val = $(this).val();
            getLatLong($val);
        });
        $("#address").on("keyup",function(){
            $val = $(this).val();
            getLatLong($val);
        });
        @if ($merchantForm->getAddress()!=null)
            getLatLong($("#address").val());
        @endif
        @if ($merchantForm->getProvinceId()!=null)
            $provinceId = $("#provinceId").val();
            $("#districtId").html(getDistrict($provinceId));
        @endif
        @if ($merchantForm->getDistrictId()!=null)
            $districtId = $("#districtId").val();
            $("#wardId").html(getWard($districtId));
        @endif
    });
    
    function getLatLong($val){
        $.ajax({
             type: "GET",
             url: "{{Asset('/portal/merchant/getLatLong')}}",
             data: "address="+$val,
             cache: false,
             success: function(response)
             {
                 var obj = jQuery.parseJSON(response);
                 initialize(obj.lat,obj.long);
                 $("#lat").val(obj.lat);
                 $("#long").val(obj.long);
             }
         });
    }
    function initialize(latitude,longitude)
    {
        var myLatLng = new google.maps.LatLng(latitude,longitude);

        var mapProp = {
            zoom:17,
            center: myLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map=new google.maps.Map(document.getElementById('map_canvas'),mapProp);

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            optimized: false,
            title:'Former About.com Headquarters'
        }); 
    }
    
    /*
     * list province - district - ward
     */
    function getProvince(){
        $html ='<option value="">{{trans("common.please_choose")}}</option>';
        @foreach ($listProvince as $province)
            $selected='';
           @if ($merchantForm->getProvinceId()!=null && $province->provinceid==$merchantForm->getProvinceId())
               $selected = 'selected'; 
           @endif 
           $html+='<option value="{{$province->provinceid}}" '+$selected+' >'+unescape("{{$province->type.' '.$province->name}}")+'</option>';
        @endforeach
        return $html;
    }
    
    function getDistrict($provinceId){
        $html ='<option value="">{{trans("common.please_choose")}}</option>';
        @foreach ($listDistrict as $district)
           if({{$district->provinceid}} == $provinceId){
               $selected='';
                @if ($merchantForm->getDistrictId()!=null && $district->districtid==$merchantForm->getDistrictId())
                    $selected = 'selected'; 
                @endif 
                $html+='<option value="{{$district->districtid}}" ' +$selected+ '>'+unescape("{{$district->type.' '.$district->name}}")+'</option>';
        }
        @endforeach
        return $html;
    }
    
    function getWard($districtId){
        $html ='<option value="">{{trans("common.please_choose")}}</option>';
        @foreach ($listWard as $ward)
           if({{$ward->districtid}} == $districtId){
               $selected='';
                @if ($merchantForm->getWardId()!=null && $ward->wardid==$merchantForm->getWardId())
                    $selected = 'selected'; 
                @endif 
                $html+='<option value="{{$ward->wardid}}" ' +$selected+' >'+unescape("{{$ward->type.' '.$ward->name}}")+'</option>';
            }
        @endforeach
        return $html;
    }
    
    
</script>
@stop
