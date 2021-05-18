<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ATMScheduler | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="/plugins/summernote/summernote-bs4.min.css">

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-DKNLBVE0R5"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-DKNLBVE0R5');
  </script>
  
  @yield('addons')

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- SEARCH FORM -->


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      @if(Session::has('user_id'))
        <?php

          $user_id = Session::get('user_id');
          $user = App\User::where('id', $user_id)->first();
          $departament = App\Departament::where('id', $user->departament_id)->first();
          $concedii_persoana = App\Concediu::where('user_id', $user_id)->whereNotNull('acceptat')->where('citit', 0)->get();
          $concedii_schimb = App\Concediu::join('concedius as schimb_concedii', 'schimb_concedii.id', '=', 'concedius.schimb_concediu_id')
                                         ->join('users', 'users.id', '=', 'schimb_concedii.user_id')
                                         ->where('concedius.user_id', $user_id)->whereNotNull('concedius.schimb_concediu_id')
                                         ->whereNull('concedius.acceptat')
                                         ->get([
                                           'concedius.*',
                                           'users.name',
                                         ]);

          $concedii_sef = [];
          $cereri_editare_concediu = [];
          $concedii_obligatorii = [];


           if($user->sef){
     				$concedii_neacceptate1 = App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
     																	 ->where('users.departament_id', $user->departament_id)
     																	 ->where('users.sef', false)
                                       ->where('acceptat_inlocuitor', 1)
     																	 ->whereNull('concedius.acceptat')
                                       ->where('concedius.citit', false)
     																   ->get([
     																	  'concedius.*',
     																		'users.name',
     																	 ]);

     				 $concedii_neacceptate2 = App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
     															->join('departaments', 'departaments.id', '=', 'users.departament_id')
     															->where('users.sef', true)
                                  ->where('acceptat_inlocuitor', 1)
     															->whereNull('concedius.acceptat')
                                  ->where('concedius.citit', false)
     															->where('departaments.departament_id', $user->departament_id)
     															->get([
     															 'concedius.*',
     															 'users.name',
     															]);

              $cereri_editare_concediu1 = App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
       																	 ->where('users.departament_id', $user->departament_id)
       																	 ->where('users.sef', false)
       																	 ->whereNotNull('concedius.acceptat')
                                         ->where('concedius.cerere_editare', 1)
       																   ->get([
       																	  'concedius.*',
       																		'users.name',
       																	 ]);

              $cereri_editare_concediu2 = App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
      															->join('departaments', 'departaments.id', '=', 'users.departament_id')
      															->where('users.sef', true)
      															->whereNotNull('concedius.acceptat')
                                    ->where('concedius.cerere_editare', 1)
      															->where('departaments.departament_id', $user->departament_id)
      															->get([
      															 'concedius.*',
      															 'users.name',
      															]);

              $angajati_zile_ramase1 = App\User::where('users.departament_id', $user->departament_id)
               																	 ->where('users.sef', false)
                                                 ->where('users.zile_alocate', '>', 0)
               																   ->get([
               																		'users.*',
               																	 ]);

              $angajati_zile_ramase2 = App\User::join('departaments', 'departaments.id', '=', 'users.departament_id')
                  															->where('users.sef', true)
                                                ->where('users.zile_alocate', '>', 0)
                  															->where('departaments.departament_id', $user->departament_id)
                  															->get([
                  															 'users.*',
                  															]);

     					foreach ($concedii_neacceptate1 as $value) {
     						array_push($concedii_sef, $value);
     					}


     					foreach($concedii_neacceptate2 as $value){
     						array_push($concedii_sef, $value);
     					}

              foreach ($cereri_editare_concediu1 as $value) {
                array_push($cereri_editare_concediu, $value);
              }

              foreach ($cereri_editare_concediu2 as $value) {
                array_push($cereri_editare_concediu, $value);
              }

              foreach($angajati_zile_ramase1 as $value){
                array_push($concedii_obligatorii, $value);
              }

              foreach($angajati_zile_ramase2 as $value){
                array_push($concedii_obligatorii, $value);
              }
     			}
     			else if($user->rol == 'director'){

     				$concedii_sef = App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
     														->join('departaments', 'departaments.id', '=', 'users.departament_id')
     														->where('users.sef', true)
                                ->where('acceptat_inlocuitor', 1)
     														->whereNull('concedius.acceptat')
                                ->where('concedius.citit', false)
     														->where('departaments.departament_id', $user->departament_id)
     														->get([
     														 'concedius.*',
     														 'users.name',
     														]);
            $cereri_editare_concediu = App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
     														->join('departaments', 'departaments.id', '=', 'users.departament_id')
     														->where('users.sef', true)
     														->whereNotNull('concedius.acceptat')
                                ->where('concedius.cerere_editare', 1)
     														->where('departaments.departament_id', $user->departament_id)
     														->get([
     														 'concedius.*',
     														 'users.name',
     														]);

            $concedii_obligatorii = App\User::join('departaments', 'departaments.id', '=', 'users.departament_id')
                 														->where('users.sef', true)
                                            ->where('users.zile_alocate', '>', 0)
                 														->where('departaments.departament_id', $user->departament_id)
                 														->get([
                 														 'users.*',
                 														]);
     			}


          $addon_notif = 0;

          if(!$user->citit_zile){
            $addon_notif++;
          }

          $concediu_editare = \App\Concediu::where('user_id', $user->id)->where('vazut_editare', false)->get();
          if(count($concediu_editare)>0){
            $addon_notif++;
          }
          $now = Carbon\Carbon::now();
          if(env('APP_DEBUG')){
            $now = Carbon\Carbon::parse('11/11/2021');
          }
          $concedii_urgent = ($now->month == 11 || $now->month == 12) && (count($concedii_obligatorii)>0);
          if(!$concedii_urgent){
            $concedii_obligatorii=[];
          }
        ?>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge @if($concedii_urgent) badge-danger @else badge-warning @endif navbar-badge">{{ count($concedii_persoana)
              + count($concedii_sef) + count($cereri_editare_concediu) + $addon_notif
              + count($concedii_schimb) + count($concedii_obligatorii) }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header">{{ count($concedii_persoana) +
              count($concedii_sef) + count($cereri_editare_concediu) + $addon_notif +
              count($concedii_schimb) + count($concedii_obligatorii) }} Notificari</span>

            @foreach ($concedii_persoana as $value)
              <div class="dropdown-divider"></div>
              <a href="/concedii" class="dropdown-item">
                <i class="fas fa-envelope mr-2 text-warning"></i> Cererea ta de concediu a fost
                @if($value->acceptat == 1)
                  acceptata
                @else
                  respinsa
                @endif

              </a>

            @endforeach

            @foreach ($concedii_sef as $value)

              <div class="dropdown-divider"></div>
              <a href="/verificare-concedii" class="dropdown-item">
                <i class="fas fa-envelope mr-2 text-warning"></i> {{$value->name}} a trimis o cerere de concediu

              </a>

            @endforeach

            @if($user->citit_zile == false)
              <div class="dropdown-divider"></div>
              <a href="/concedii" class="dropdown-item">
                <i class="fas fa-envelope mr-2 text-warning"></i> Ti-au fost modificat zilele alocate.
              </a>
            @endif
            @foreach ($cereri_editare_concediu as $value)



              <div class="dropdown-divider"></div>
              <a href="/concedii-tot#concediu-{{$value->id}}" class="dropdown-item">
                <i class="fas fa-envelope mr-2 text-warning"></i> {{$value->name}} a trimis o cerere de editare
              </a>


            @endforeach

              @if(count($concediu_editare)>0)
                <div class="dropdown-divider"></div>
                <a href="/concedii" class="dropdown-item">
                  <i class="fas fa-envelope mr-2 text-warning"></i> Cererea ta de editare a fost acceptata!
                </a>
              @endif

              @foreach ($concedii_schimb as $value)



                <div class="dropdown-divider"></div>
                <a href="/concedii" class="dropdown-item">
                  <i class="fas fa-envelope mr-2 text-warning"></i> {{$value->name}} a trimis o cerere de schimb
                </a>


              @endforeach

              @if($concedii_urgent)
                @foreach ($concedii_obligatorii as $value)
                  <div class="dropdown-divider"></div>
                  <a href="/concediu-obligatoriu/{{$value->id}}" class="dropdown-item">
                    <i class="fas fa-exclamation-circle text-danger"></i> {{$value->name}} - Zile alocate ramase
                  </a>
                @endforeach
              @endif
          </div>
        </li>

      @endif

      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->

	@php

		$user = \App\Http\Controllers\Controller::get_user();

	@endphp

	  @if($user)
      <?php


        $now = Carbon\Carbon::now();
        $data_resetare = Carbon\Carbon::parse($user->data_resetare_zile);

        if($now->year != $data_resetare->year){
          $user->zile_alocate = 21;
          $user->data_resetare_zile = $now;
          $user->save();
        }


        $cale = '/poze-profil/';
        $fisier = $user->id.'.jpg';

        $poza = null;

        if(\File::exists(public_path().$cale.$fisier)){
          $poza = asset($cale.$fisier);
        }else{
          $poza = asset($cale.'default.png');
        }
      ?>
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ $poza }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="/profile" class="d-block">{{ $user->name }}</a>
        </div>
      </div>
	  @endif


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
			   @if($user != null)
			   @if($user->rol == "administrator")
          <li class="nav-item">
            <a href="/" class="nav-link">
              <i class="nav-icon far fa-circle text-info"></i>
              <p>Statistici</p>
            </a>
          </li>
				@endif
				@endif

        @if($user != null)
        @if($user->rol == "administrator")
         <li class="nav-item">
           <a href="/departamente" class="nav-link">
             <i class="nav-icon far fa-circle text-info"></i>
             <p>Gestiune Departamente</p>
           </a>
         </li>
       @endif
       @endif

       @if($user != null)
       @if($user->rol != "administrator")
        <li class="nav-item">
          <a href="/cereri-inlocuitor" class="nav-link">
            <i class="nav-icon far fa-circle text-info"></i>
            <p>Cereri inlocuitor</p>
          </a>
        </li>
      @endif
      @endif

        @if($user != null)
        @if($user->rol != "administrator")
         <li class="nav-item">
           <a href="/verificare-concedii" class="nav-link">
             <i class="nav-icon far fa-circle text-info"></i>
             <p>Cereri Concediu</p>
           </a>
         </li>
       @endif
       @endif

       @if($user != null)
       @if($user->rol != "administrator")
        <li class="nav-item">
          <a href="/istoric-departament" class="nav-link">
            <i class="nav-icon far fa-circle text-info"></i>
            <p>Istoric Departament</p>
          </a>
        </li>
      @endif
      @endif

        @if($user != null)
         <li class="nav-item">
           <a href="/angajati" class="nav-link">
             <i class="nav-icon far fa-circle text-info"></i>
             <p>Angajati</p>
           </a>
         </li>
       @endif

       @if($user != null && $user->rol != 'administrator')
        <li class="nav-item">
          <a href="/istoric-concedii" class="nav-link">
            <i class="nav-icon far fa-circle text-info"></i>
            <p>Istoric Concedii</p>
          </a>
        </li>
      @endif
			@if($user != null && $user->rol != 'administrator')
		<li class="nav-item">
            <a href="/concedii" class="nav-link">
              <i class="nav-icon far fa-circle text-info"></i>
              <p>Concedii</p>
            </a>
          </li>
		  @endif
		  @if($user != null)
			   @if($user->rol == "administrator")
		  <li class="nav-item">
            <a href="/register" class="nav-link">
              <i class="nav-icon far fa-circle text-info"></i>
              <p>Inregistrare Cont</p>
            </a>
          </li>
		  @endif
				@endif
        @if($user != null)
          @if($user->rol == "sef-departament" || $user->rol == "director")
         <li class="nav-item">
           <a href="/raport/{{$departament->id}}" class="nav-link">
             <i class="nav-icon far fa-circle text-info"></i>
             <p>Raport</p>
           </a>
         </li>
         @endif
       @endif
			@if($user != null)
		<li class="nav-item">
            <a href="/logout" class="nav-link">
              <i class="nav-icon far fa-circle text-info"></i>
              <p>Logout</p>
            </a>
          </li>
		  @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('title')</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

	@if(session('status'))
    <div class="alert alert-warning" role="alert">
  		{{ session('status') }}
    </div>
	@endif

	@if($user && App\Http\Controllers\Controller::in_concediu($user->id)){
		<p class="text-danger">Esti in concediu!</p>

  @endif
  @if($user)
    @php

     $concediu = App\Http\Controllers\Controller::ap_concediu($user->id);
    @endphp

    @if($concediu)
    @php
     $data = Carbon\Carbon::parse($concediu->data_inceput);
     $now = Carbon\Carbon::now();
     $diff = $data->diffInDays($now);
     @endphp

     @if($diff<=7)
      <p class="text-danger">Mai ai {{$diff}} zile pana la concediu</p>
    @endif
  @endif
  @endif

	@yield('content')

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; {{ Carbon\Carbon::now()->year }} <a href="/">{{ config('app.name') }}</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.1.0-rc
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="/plugins/chart.js/Chart.min.js"></script>

<!-- jQuery Knob Chart -->
<script src="/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="/plugins/moment/moment.min.js"></script>
<script src="/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->`
<script src="/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/adminlte.js"></script>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/609fd7a8b1d5182476b9263d/1f5o64qsv';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

@yield('addons-down')

</body>
</html>
