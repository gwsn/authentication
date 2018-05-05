<?php

Route::post('/account', 'AccountController@create');
Route::get('/account/test/email/{accountGUID}', 'AccountController@testEmail');
Route::post('/account/login', 'AccountController@login');
Route::post('/account/{hash}/verify', 'AccountController@verify');

Route::group(['middleware' => ['account.auth']], function () {
    Route::get('/account/{accountGUID}', 'AccountController@read');
    Route::put('/account/{accountGUID}', 'AccountController@update');
    Route::put('/account/{accountGUID}/password', 'AccountController@changePassword');
    Route::delete('/account/{accountGUID}', 'AccountController@delete');
});


