<?php

Route::get('/create', 'RegiestController@create')->name('create');

Route::get('/confirm/{token}', 'RegiestController@confirm')->name('confirm');
Route::get('/test', function () {
  return view('verifiedEmailFail', [
    'type' => 'tokenTimeout',
    'msg' => '链接已过期，请重新提交注册信息',
  ]);
});

Route::middleware(['checkParams'])->group(function () {

  Route::post('/store', 'RegiestController@store')->name('store');

});
