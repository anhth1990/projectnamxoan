<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{Asset('public/Admin/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image" />
        </div>
        <div class="pull-left info">
          <p>{{Session::get('login_admin_portal')->getFullName()}}</p>

          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search..."/>
          <span class="input-group-btn">
            <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MENU</li>
        <li class="<?php if($nav==1)echo'active'; ?>">
          <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL'))}}">
            <i class="fa fa-dashboard"></i> <span>{{trans('dashboard.title_page')}}</span> <!-- <i class="fa fa-angle-left pull-right"></i> -->
          </a>
        </li>
        <li class="<?php if($nav==3)echo'active'; ?>">
          <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id')}}">
            <i class="fa fa-ambulance"></i> <span>{{trans('portal/common.manager')}}</span> <!-- <i class="fa fa-angle-left pull-right"></i> -->
          </a>
        </li>
        <!--
        <li class="<?php if($nav==2)echo'active'; ?> treeview">
            <a href="#">
              <i class="fa fa-group"></i>
              <span>{{trans('merchant.title')}}</span>
            </a>
            <ul class="treeview-menu">
                <li class="<?php if($nav==2 && $sub==1)echo'active'; ?>">
                    <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL'))}}/merchant/list">
                        <i class="fa fa-circle-o"></i> {{trans('common.list')}}
                    </a>
                </li>
            </ul>
        </li>
        -->
        <!--
        <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Danh sách</span>
            <span class="fa fa-angle-left pull-right"></span>
          </a>
          <ul class="treeview-menu">
            <li class=""><a href="pages/custom/list.html"><i class="fa fa-circle-o"></i> Danh sách mẫu</a></li>
            <li class=""><a href="pages/custom/list_full.html"><i class="fa fa-circle-o"></i> Danh sách đầy đủ</a></li>
          </ul>
        </li>
        -->
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
