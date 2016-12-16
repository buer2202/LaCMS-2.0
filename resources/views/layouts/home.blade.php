<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seo['title'] or '公司官网' }}</title>
    <meta name="description" content="{{ $seo['keywords'] or '公司官网' }}">
    <meta name="keywords" content="{{ $seo['description'] or '公司官网' }}">

    @include('layouts.cdn')
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <script type="text/javascript" src="/plugins/bootstrap/js/respond.min.js"></script>
    <script type="text/javascript" src="/js/mylib.js"></script>

    @yield('jsLibrary')
</head>
<body>
    @include('layouts.homeHeader')
    @yield('content')
    @include('layouts.homeFooter')
    @yield('js')
</body>
</html>
