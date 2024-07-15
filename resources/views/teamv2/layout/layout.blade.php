@include('teamv2.partials.header')

<body class="hold-transition sidebar-mini layout-fixed dash-page">

<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <!-- Logout Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button">
          <i class="fas fa-cog"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="{{route('team.logout')}}" class="dropdown-item">
          <i class="fas fa-arrow-right"></i> sortir
          </a>
        </div>
      </li>
    </ul>
    </ul>

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
      <img class="logo-login" src="{{ asset('/team/imgs/app_icon.png') }}" >
      <span class="brand-text font-weight-light">{{ __('team.backoffice_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="/dashboard" class="nav-link {{route_get_class('dashboard')}}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
              Tableau de bord
              </p>
            </a>
          </li>
          <!-- <li class="nav-item">
            <a href="/map" class="nav-link {{route_get_class('map')}}">
            <i class="nav-icon fas fa-map-marked-alt"></i>
              <p>
              Carte du monde
              </p>
            </a>
          </li> -->
          <li class="nav-item">
            <a href="/params" class="nav-link {{route_get_class('params')}}">
              <i class="nav-icon fas fa-sliders-h"></i>
              <p>
                Paramètres
                <!-- <span class="badge badge-info right">2</span> -->
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/users" class="nav-link {{route_get_class('users')}}">
              <i class="nav-icon fas fa-users"></i>
              <p>
              Utilisateurs
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/contacts" class="nav-link {{route_get_class('contacts')}}">
              <i class="nav-icon far fa-comments"></i>
              <p>
              Site contact
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/newusers" class="nav-link {{route_get_class('newusers')}}">
              <i class="nav-icon far fa-user-circle"></i>
              <p>
              Site register
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/texts" class="nav-link {{route_get_class('texts')}}">
            <i class="nav-icon fas fa-edit"></i>
              <p>
              Des textes
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/faqs" class="nav-link {{route_get_class('faqs')}}">
            <i class="nav-icon fas fa-question-circle"></i>
              <p>
              FAQ
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->

    <!-- /.sidebar-custom -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      @include('teamv2.partials.messages')

    <!-- .content -->
      @yield('content')
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  @include('teamv2.partials.footer-div')

</div>
<!-- ./wrapper -->

@include('teamv2.partials.footer')
