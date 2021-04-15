<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>个人注册</title>
  <link rel="stylesheet" href="{{mix('css/app.css')}}">
</head>
<body>
  
  <div class="w-full h-full flex flex-col">

    {{-- 头部 --}}
    @include('common._header')

    {{-- 主体 --}}
    <div class="w-full flex-1">
      <div class="w-full h-full md:w-9/12 mx-auto flex-1">
        
        {{-- 表单 --}}
        <form id="form" class="h-full md:w-4/12 mx-auto flex items-center" method="post" action="/store">
        
          <div class="w-full relative">
            <x-input type="text" rule="username" placeholder="请输入用户名" class="mb-4" jshook="username" />
            <x-input type="email" rule="email" placeholder="请输入邮箱" class="mb-4" jshook="email" />
            <x-input type="password" rule="password" placeholder="请输入密码" class="mb-4" jshook="password" />
            <x-button type="danger" isSubmit value="提交" class="w-full h-10" />
            <div class="hidden w-full h-full bg-gray-700 opacity-25 cursor-not-allowed absolute left-0 top-0" jshook="formShade"></div>
          </div>

          @csrf
        </form>

      </div>
    </div>

    {{-- 底部 --}}
    @include('common._header')

  </div>

<script src="{{mix('js/manifest.js')}}"></script>
<script src="{{mix('js/vendor.js')}}"></script>
<script src="{{mix('js/regiest.js')}}"></script>
</body>
</html>