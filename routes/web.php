<?php

// 注册
Route::prefix('/regiest')->group(function () {

  // 注册页面
  Route::get('/', 'StaticPageController@regiest')->name('regiestPage');

  // 中间件验证
  Route::middleware(['checkParams'])->group(function () {
    
    // 提交发送邮件请求
    Route::post('/sendEmail', 'UserController@sendEmailRegiest')->name('sendEmailRegiest');
  
  });

  // 确认注册链接
  Route::get('/confirm/{token}', 'UserController@regiestConfirm')->name('regiestConfirm');

});


// 修改密码
Route::prefix('/password')->group(function () {

  // 申请修改的界面
  Route::get('/', 'StaticPageController@tPassword')->name('passwordPage');
  
  // 修改表单界面
  Route::get('/confirm/{token}', 'UserController@resetPwdConfirm')->name('resetPwdConfirm');

  Route::middleware(['checkParams'])->group(function () {
    
    // 提交邮件发送请求
    Route::post('/sendEmail', 'UserController@sendEmailResetPwd')->name('sendEmailResetPwd');
  
    // 提交新密码
    Route::put('/change', 'UserController@changePwd')->name('changePwd');

  });

});


// 登录
Route::prefix('/login')->group(function () {

  // 登录界面
  Route::get('/', 'StaticPageController@login')->name('loginPage');

  // 登出
  Route::post('/logout', 'UserController@logout')->name('logout');
  
  Route::middleware(['checkParams'])->group(function () {
    
    // 提交登录请求
    Route::post('/singIn', 'UserController@singIn')->name('singIn');

  });


});


// 不需要前缀的路由
Route::get('/captcha', 'UserController@captcha')->name('captcha');
Route::get('/', 'StaticPageController@indexPage')->name('indexPage');