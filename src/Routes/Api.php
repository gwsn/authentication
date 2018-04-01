<?php

Route::post('/account', 'AccountController@create');
Route::post('/account/login', 'AccountController@login');

Route::group(['middleware' => ['authentication']], function () {
    Route::get('/account/{accountGUID}', 'AccountController@read');
    Route::put('/account/{accountGUID}', 'AccountController@update');
    Route::put('/account/{accountGUID}/password', 'AccountController@changePassword');
    Route::delete('/account/{accountGUID}', 'AccountController@delete');
});


