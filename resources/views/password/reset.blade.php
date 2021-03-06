@extends('layout.regiest_layout')
@section('title', '重置密码')
@section('js', '/js/passwordReset.js')

@section('content')

  {{-- 主体 --}}
  <div class="w-full h-full md:w-9/12 mx-auto flex-1">
    
    {{-- 表单 --}}
    <form id="form" class="h-full md:w-4/12 mx-auto flex items-center" method="post" action="/login">
    
      <div class="w-full relative">
        <x-input type="email" rule="email" placeholder="请输入邮箱" class="mb-4" jshook="email" />
        <x-input type="text" rule="required" icon="verifyCode" placeholder="请输入验证码" class="mb-2" jshook="captcha">
          <x-slot name="code">
            <div class="w-32 ml-3 border border-gray-200 box-border rounded-md overflow-hidden" jshook="captchaImg">
              <img src="/captcha?captchaType=passwordReset&w=128&h=48">
            </div>
          </x-slot>
        </x-input>
        <x-button type="danger" isSubmit value="发送邮件" class="w-full h-10" />
        <div class="hidden w-full h-full bg-gray-700 opacity-25 cursor-not-allowed absolute left-0 top-0" jshook="formShade"></div>
      </div>

      @csrf
    </form>

  </div>

@endsection