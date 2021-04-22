<?php

// 注册
Route::prefix('/regiest')->group(function () {

  // 注册页面
  Route::get('/', 'StaticPageController@regiest')->middleware(['refreshAuth'])->name('regiestPage');

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
  Route::get('/', 'StaticPageController@login')
    ->middleware(['refreshAuth', 'isLogged'])
    ->name('loginPage');
  
  Route::middleware(['checkParams'])->group(function () {
    
    // 提交登录请求
    Route::post('/singIn', 'SessionController@singIn')->name('singIn');

  });


});


// 登出
Route::prefix('/logout')->group(function () {

  // 普通登出
  Route::get('/', 'SessionController@logout')->name('logout');
  
  Route::get('/sso', 'SessionController@ssoLogout')->name('ssoLogout');

});


Route::get('/', 'StaticPageController@indexPage')->middleware(['refreshAuth'])->name('indexPage');
Route::get('/captcha', 'UserController@captcha')->name('captcha');
Route::get('/test', 'UserController@test');

// 验证ST是否有效
Route::post('/check_st', 'SessionController@checkSt')->name('checkSt');

// 验证tgc是否有效
Route::post('/check_tgc', 'SessionController@checkTgc')->middleware('checkParams')->name('checkTgc');


// 获取用户信息
Route::prefix('/info')->group(function () {

  Route::middleware(['checkParams'])->group(function () {

    Route::get('/', 'SessionController@userInfo')->name('getUserInfo');

  });


});