@extends('layout.regiest_layout')
@section('title', '链接失效')

@section('content')

  <div class="md:w-4/12 h-full mx-auto flex items-center">
    <div class="w-full">
      <p class="border rounded-md border-gray-100 bg-blue-800 text-white text-center py-2 relative -mt-8">{{$msg}}</p>
      <div class="mt-5 flex flex-row justify-around">
        <a href="{{route('create')}}" class="text-blue-600 font-semibold text-xl hover:underline">登录</a>
        <a href="{{route('create')}}" class="text-blue-600 font-semibold text-xl hover:underline">注册</a>
        <a href="{{route('create')}}" class="text-blue-600 font-semibold text-xl hover:underline">找回密码</a>
      </div>
    </div>
  </div>

@endsection