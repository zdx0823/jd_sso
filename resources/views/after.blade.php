@extends('layout.regiest_layout')
@if (Auth::check())
    
  @section('title', '已登录')

@else

  @section('title', '未登录')

@endif
@section('js', '/js/after.js')
@section('content')

  {{-- 主体 --}}
  <div class="w-full h-full md:w-9/12 mx-auto flex-1">
    
    <div class="md:w-4/12 h-full mx-auto flex items-center">
      <div class="w-full">

        
        <p class="
          border rounded-md border-gray-100 
          bg-blue-800 
          text-white text-center
          py-2 relative -mt-8"
        >
          @if(Auth::check())
            您已登录，请选择要访问的页面
          @else
            您还未登录，请点击下面链接进行登录
          @endif
        </p>

        <div class="mt-5 flex flex-row justify-around">
          @if(!Auth::check())
            <a href="{{route('loginPage')}}" class="text-blue-600 font-semibold text-xl hover:underline">登录</a>
            <a href="{{route('regiestPage')}}" class="text-blue-600 font-semibold text-xl hover:underline">注册</a>
            <a href="{{route('passwordPage')}}" class="text-blue-600 font-semibold text-xl hover:underline">找回密码</a>
          @else
            <a href="{{route('passwordPage')}}" class="text-blue-600 font-semibold text-xl hover:underline">重置密码</a>
          @endif
        </div>

        <div class="mt-5 flex flex-row justify-around">
          <a href="" class="text-blue-600 font-semibold text-xl hover:underline">云盘</a>
          <a href="" class="text-blue-600 font-semibold text-xl hover:underline">商城首页</a>
        </div>

        @if (Auth::check())
          <div class="mt-5 flex flex-row justify-around">
            <a href="javascript:;" class="text-blue-600 font-semibold text-xl hover:underline" jshook="logout">退出登录</a>
          </div>
        @endif
      </div>
    </div>

  </div>

@endsection