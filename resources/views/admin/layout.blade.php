<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    {{-- Used for Page titles and general page description --}}
    <title>
        @if (! App::environment('production'))
            [{{ strtoupper(App::environment()) }}]
        @endif
        
        @if (trim($__env->yieldContent('title')))
            @yield('title')
        @else
            Reflexions Admin
        @endif
    </title>

    {{-- @include('components.global.favicons') --}}

    <!-- Open Sans font from Google CDN -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300&subset=latin" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ Content::elixir('css/vendor.css') }}">
    <link rel="stylesheet" href="{{ Content::elixir('css/admin.css') }}">
    @yield('style')
    @stack('styles')
</head>

<body id="admin" class="admin @section('body-class') theme-default  @show main-menu-animated main-navbar-fixed">
    <div id="main-wrapper">
        {{-- -----------------------------------------------------
             START #main-navbar
             ----------------------------------------------------- --}}
        <div id="main-navbar" class="navbar navbar-inverse" role="navigation">
            <div class="navbar-inner">
                <!-- Main navbar header -->
                <div class="navbar-header">
                    @section('navbar-header')
                    <!-- Logo -->
                    <a href="/" class="navbar-brand">
                        <div><img alt="Home" src="/vendor/content/images/pixel-admin/main-navbar-logo.png"></div>
                        Home
                    </a>
                    @show

                    <!-- Main navbar toggle -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar-collapse"><i class="navbar-icon fa fa-bars"></i></button>
                </div> <!-- / .navbar-header -->

                <div id="main-navbar-collapse" class="collapse navbar-collapse main-navbar-collapse">
                    <div>
                        <ul class="nav navbar-nav">
                            @section('main-navbar')

                            @show
                        </ul> <!-- / .navbar-nav -->

                        <div class="right clearfix">
                            <ul class="nav navbar-nav pull-right right-navbar-nav">
                                @section('main-navbar-right')
                                
                                @show
                            </ul> <!-- / .navbar-nav -->
                        </div> <!-- / .right -->
                    </div>
                </div> <!-- / #main-navbar-collapse -->
            </div> <!-- / .navbar-inner -->
        </div> <!-- / #main-navbar -->
        {{-- -----------------------------------------------------
             END #main-navbar
             ----------------------------------------------------- --}}

        {{-- -----------------------------------------------------
             START #main-menu
             ----------------------------------------------------- --}}
        <div id="main-menu" role="navigation">
            <div id="main-menu-inner">
                @if (trim($__env->yieldContent('main-menu-content-top')))
                <div class="menu-content top" id="menu-content-demo">
                    @yield('main-menu-content-top')
                </div>
                @endif
                @if (trim($__env->yieldContent('main-menu-navigation')))
                <ul class="navigation">
                    @yield('main-menu-navigation')
                </ul> <!-- / .navigation -->
                @endif
                @if (trim($__env->yieldContent('main-menu-content-bottom')))
                <div class="menu-content">
                    @yield('main-menu-content-bottom')
                </div>
                @endif
            </div> <!-- / #main-menu-inner -->
        </div> <!-- / #main-menu -->

        {{-- -----------------------------------------------------
             END #main-menu
             ----------------------------------------------------- --}}

        <div id="main-menu-bg"></div>

        <div id="content-wrapper">
            @if (trim($__env->yieldContent('breadcrumb')))
                <ul class="breadcrumb breadcrumb-page">
                <div class="breadcrumb-label text-light-gray">You are here: </div>
                    @section('breadcrumb-wrapper')
                        @yield('breadcrumb')
                    @show
                </ul>
            @endif

            @if (trim($__env->yieldContent('page-header')))
                <div class="page-header">
                    <div class="row">
                        <!-- Page header, center on small screens -->
                        <h1 class="col-xs-12 col-sm-6 text-center text-left-sm">@yield('page-header')</h1>
                        <div class="col-xs-12 col-sm-6 pull-right">@yield('page-header-right')</div>
                    </div>

                    <div class="row" style="margin-top: 10px;">@yield('page-filters')</div>
                </div> <!-- / .page-header -->
            @endif

            @yield('main-content')
        </div>

    </div>

    @yield('modals')

    <script>
    var CKEDITOR_BASEPATH = '/vendor/content/ckeditor/';
    </script>
    <!-- Pixel Admin's javascripts -->
    <script src="{{ Content::elixir('js/vendor.js') }}"></script>
    <script src="{{ Content::elixir('js/admin.js') }}"></script>
    
    {{-- @include('components.global.flash') --}}
    <script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });

    //start PixelAdmin
    window.PixelAdmin.start();
    </script>
    
    
    @yield('flash-message')


    @yield('javascript')
    @stack('scripts')

</body>
</html>
