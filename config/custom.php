<?php
return [

  // timeout类
  'timeout' => [
    // token时效
    'token' => [
      'regiest' => env('REGIEST_TOKEN_TIMEOUT'),
      'password_reset' => env('PASSWORD_RESET_TOKEN_TIMEOUT'),
      'st' => env('ST_TOKEN_TIMEOUT')
    ],

    // 登录有效期
    'login' => [
      'default' => env('LOGIN_DEFAULT_TIMEOUT'),
      'remember' => env('LOGIN_REMEMBER_TIMEOUT'),
    ],

  ],

  // 验证码，session下标
  'session' => [
    'captcha' => [
      'login' => env('APP_NAME') . '_session_captcha_login_key',
      'password_reset' => env('APP_NAME') . '_session_captcha_password_reset_key',
    ],
    'user' => env('APP_NAME') . '_session_user_key'
  ],


  // cookie名
  'cookie' => [
    'first_session' => env('APP_NAME') . '_tmp_first_session'
  ],

  // 对称加密key
  'crypt_key' => env('CRYPT_KEY'),

  'away' => [
    'clodedisk' => env('AWAY_CLODEDISK')
  ],
];