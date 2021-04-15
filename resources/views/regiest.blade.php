<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>个人注册</title>
  <link rel="stylesheet" href="{{mix('css/regiest.css')}}">
</head>
<body>
  
  <div class="w-full h-full flex flex-col">

    {{-- 头部 --}}
    <div class="w-full h-32 border-b">

      <div class="flex flex-row items-center justify-between w-full h-full md:w-9/12 mx-auto">
        <a href="javascript:;" class="logo"></a>
        
        <p class="text-sm self-end">
          已有账号？
          <a href="javascript:;" class="text-red-600 hover:underline hover:text-red-700">请登录 > </a>
        </p>
      </div>

    </div>

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
    <div class="w-full h-24 bg-gray-200 flex flex-row items-center">
      <div class="md:w-9/12 mx-auto text-center space-x-2 text-xs text-gray-600">
        <a href="javascript:;">关于我们</a>
        <a href="javascript:;">联系我们</a>
        <a href="javascript:;">联系客服</a>
        <a href="javascript:;">合作招商</a>
        <a href="javascript:;">商家帮助</a>
        <a href="javascript:;">营销中心</a>
        <a href="javascript:;">手机京东</a>
        <a href="javascript:;">友情链接</a>
        <a href="javascript:;">销售联盟</a>
        <a href="javascript:;">京东社区</a>
        <a href="javascript:;">风险监测</a>
        <a href="javascript:;">隐私政策</a>
        <a href="javascript:;">京东公益</a>
        <a href="javascript:;">English Site</a>
        <a href="javascript:;">Media & IR</a>
      </div>
    </div>

  </div>

<script src="{{mix('js/manifest.js')}}"></script>
<script src="{{mix('js/vendor.js')}}"></script>
<script src="{{mix('js/regiest.js')}}"></script>
</body>
</html>