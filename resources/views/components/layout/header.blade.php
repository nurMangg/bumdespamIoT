<?php

    use App\Models\Roles;
    use App\Models\Menu;
    use App\Models\SettingWeb;



    $rolemenu = Roles::where('roleId', Auth::user()->userRoleId)->first();
    $menuIds = json_decode($rolemenu->roleMenuId, true);

    // Ambil semua menu sesuai dengan menu_id
    $menus = Menu::whereIn('menuId', $menuIds)->orderBy('menuOrder')->get();

    // Ambil menu utama (parent_id = null)
    $megamenus = Menu::whereNull('menuParentId')
        ->orderBy('menuOrder')
        ->get()
        ->filter(function ($menu) use ($menus) {
            // Hanya tampilkan menu utama jika memiliki sub-menu dalam daftar menu_id
            return $menus->contains('menuParentId', $menu->menuId);
        });

    $settingWeb = SettingWeb::first();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settingWeb->settingWebNama ? $settingWeb->settingWebNama . ' | PDAM' : env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}" type="image/x-icon">

    {{-- PWA --}}
    <meta name="theme-color" content="#6777ef"/>
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    {{-- TOASTR --}}
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

    {{-- Datatables --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.4.3/js/dataTables.scroller.min.js"></script>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free-6.7.1-web/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css')}}">

    @stack('script-header')
</head>
@stack('style-header')
<style>
    .nav-pills .nav-link.active {
        background-color: #608BC1;
    }

    .btn-blue {
        background-color: #608BC1;
        color: white;
    }

    .btn-blue:hover {
        background-color: white;
        color: #608BC1;
        border: #777777 solid 1px;
    }

    .btn-out-blue {
        background-color: white;
        color: #608BC1;
        border: #777777 solid 1px;
    }

    .btn-out-blue:hover {
        background-color: #608BC1;
        color: white;
    }

    .btn-outline-blue {
        --tblr-btn-color: #608BC1;
        --tblr-btn-bg: transparent;
        --tblr-btn-border-color: #608BC1;
        --tblr-btn-hover-color: var(--tblr-primary-fg);
        --tblr-btn-hover-border-color: transparent;
        --tblr-btn-hover-bg: #608BC1;
        --tblr-btn-active-color: var(--tblr-primary-fg);
        --tblr-btn-active-bg: #608BC1;
        --tblr-btn-disabled-color: #608BC1;
        --tblr-btn-disabled-border-color: #608BC1;
    }

    label:not(.form-check-label):not(.custom-file-label) {
        font-weight: normal;
    }

</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}" alt="AdminLTELogo" height="160"
                width="160">
        </div>

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
                <!-- Navbar Search -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user-circle" style="font-size: 20px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <!-- User Info -->
                        <div class="dropdown-header text-center">
                            <strong>{{ Auth::user()->name ?? 'Unknown' }}</strong>
                            <p class="text-muted text-sm">{{ Auth::user()->role->roleName ?? 'Unknown' }}</p>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-divider"></div>
                        <!-- Logout Button -->
                        <form action="/logout" method="POST">
                            @csrf
                            <!-- Tambahkan CSRF jika menggunakan Laravel -->
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar" style="background-color: #ffffff;">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">{{ $settingWeb->settingWebNama ?? "BUMDES PDAM"}}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('input-tagihan-iot.index') }}" class="nav-link {{ Route::is('input-tagihan-iot.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Input Tagihan</p>
                            </a>
                        </li>
                        @foreach ($megamenus as $megaM)
                            @php
                                // Ambil sub-menu yang terkait dengan menu utama ini
                                $subMenus = $menus->where('menuParentId', $megaM->menuId);
                            @endphp

                            @if ($subMenus->count() > 0)
                                <li class="nav-item {{ Request::is($megaM->menuRoute . '/*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is($megaM->menuRoute . '/*') ? 'active' : '' }}">
                                        <i class="nav-icon {{ $megaM->menuIcon }}"></i>
                                        <p>
                                            {{ $megaM->menuName }}
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach ($subMenus as $item)
                                            <li class="nav-item">
                                                <a href="{{ route($item->menuRoute )}}" class="nav-link {{ Route::is($item->menuRoute) ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>{{ $item->menuName }}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
