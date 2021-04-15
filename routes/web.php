<?php

Route::get('/create', 'RegiestController@create')->name('create');
Route::get('/confirm/{token}', 'RegiestController@confirm')->name('confirm');
Route::get('/login', 'RegiestController@loginPage')->name('loginPage');

Route::get('/test', 'RegiestController@test');

Route::middleware(['checkParams'])->group(function () {

  Route::post('/store', 'RegiestController@store')->name('store');
  Route::get('/captcha', 'RegiestController@captcha')->name('captcha');
  Route::post('/login', 'RegiestController@login')->name('login');

});
