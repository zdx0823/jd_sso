<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'jsSSO')</title>
  <link rel="stylesheet" href="{{mix('css/app.css')}}">
</head>
<body>
  
  <div class="w-full h-full flex flex-col">

    {{-- 头部 --}}
    @include('common._header')

    {{-- 主体 --}}
    <div class="w-full flex-1">
      @yield('content')
    </div>

    {{-- 底部 --}}
    @include('common._footer')

  </div>

<script src="{{mix('js/manifest.js')}}"></script>
<script src="{{mix('js/vendor.js')}}"></script>
<script src="@yield('js')"></script>
</body>
</html>