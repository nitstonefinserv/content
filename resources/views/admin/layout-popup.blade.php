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
    @stack('styles')
</head>

<body id="admin" class="admin @section('body-class') theme-default ">
    @yield('main-content')
    
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
    </script>
    
    @yield('javascript')
    @stack('scripts')

</body>
</html>
