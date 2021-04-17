@extends('layout.regiest_layout')
@section('title', '登录')
@section('js', 'js/login.js')

@section('content')

  {{-- 主体 --}}
  <div class="w-full h-full md:w-9/12 mx-auto flex-1">
    
    {{-- 表单 --}}
    <form id="form" class="h-full md:w-4/12 mx-auto flex items-center" method="post" action="/login">
    
      <div class="w-full relative">
        <x-input type="email" rule="email" placeholder="请输入邮箱" class="mb-4" jshook="email" />
        <x-input type="password" rule="required" placeholder="请输入密码" class="mb-4" jshook="password" />
        <x-input type="text" rule="required" icon="verifyCode" placeholder="请输入验证码" class="mb-2" jshook="captcha">
          <x-slot name="code">
            <div class="w-32 ml-3 border border-gray-200 box-border rounded-md overflow-hidden" jshook="captchaImg">
              <img src="/captcha?captchaType=login&w=128&h=48">
            </div>
          </x-slot>
        </x-input>
        <div class="mb-4 clearfix">
          <div class="float-left">
            <input id="rememberBox" type="checkbox" class="w-4 h-4" jshook="remember">
            <label for="rememberBox" class="text-sm text-gray-700 align-top select-none">7天免登录</label>
          </div>
          <a href="{{route('passwordPage')}}" class="float-right text-sm text-blue-700 hover:underline">找回密码</a>
        </div>
        <x-button type="danger" isSubmit value="登录" class="w-full h-10" />
        <div class="hidden w-full h-full bg-gray-700 opacity-25 cursor-not-allowed absolute left-0 top-0" jshook="formShade"></div>
      </div>

      @csrf
    </form>

  </div>

@endsection