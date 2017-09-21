<section class="content-header">
  <h1>
    {{$titleModule}}
    <small>{{$titleModuledetail}}</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL'))}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
    @if(isset($navModule) && $navModule!='')
    <li><a href="{{$linkModule}}">{{$navModule}}</a></li>
    @endif
    <li class="active">{{$navActive}}</li>
  </ol>
</section>