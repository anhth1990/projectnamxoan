<header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo"><b>Admin</b>Portal</a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->

          <!-- Notifications: style can be found in dropdown.less -->

          <!-- Tasks: style can be found in dropdown.less -->

          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{Asset('public/Admin/dist/img/user2-160x160.jpg')}}" class="user-image" alt="User Image"/>
              <span class="hidden-xs">{{Session::get('login_admin_portal')->getFullName()}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{Asset('public/Admin/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image" />
                <p>
                  {{Session::get('login_admin_portal')->getEmail()}}
                  <!--<small>Member since Nov. 2012</small>-->
                </p>
              </li>
              <!-- Menu Body -->

              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="javascript:void(0)" onclick="alert('Updating...')" class="btn btn-default btn-flat">{{trans('common.profile')}}</a>
                </div>
                <div class="pull-right">
                  <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/logout')}}" class="btn btn-default btn-flat">{{trans('common.sign_out')}}</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>