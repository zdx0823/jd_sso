<?php

Route::get('/create', 'RegiestController@createPage')->name('createPage');
Route::get('/test', function () {
  return view('test');
});

Route::middleware(['checkParams'])->group(function () {

  Route::post('/store', 'RegiestController@store')->name('store');

});
