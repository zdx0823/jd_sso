@extends('layout.regiest_layout')
@section('title', '重置密码')
@section('js', '/js/passwordResetForm.js')

@section('content')

  {{-- 主体 --}}
  <div class="w-full h-full md:w-9/12 mx-auto flex-1">
    
    {{-- 表单 --}}
    <form id="form" class="h-full md:w-4/12 mx-auto flex items-center" method="post" action="/login">
    
      <div class="w-full relative">
        <x-input type="password" rule="password" placeholder="请输入密码" class="mb-4" jshook="pass1" />
        <x-input type="password" rule="password|equal:pass1" placeholder="请再次输入密码" class="mb-4" jshook="pass2" />
        <x-button type="danger" isSubmit value="确定" class="w-full h-10" />
        <div class="hidden w-full h-full bg-gray-700 opacity-25 cursor-not-allowed absolute left-0 top-0" jshook="formShade"></div>
      </div>

      <input type="hidden" name="resetPwdToken" value="{{$resetPwdToken}}">
      @csrf
    </form>

  </div>

@endsection