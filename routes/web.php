<?php

Route::get('/create', 'RegiestController@createPage')->name('createPage');

Route::middleware(['checkParams'])->group(function () {

  Route::post('/store', 'RegiestController@store')->name('store');

});
