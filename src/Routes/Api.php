<?php

Route::post('/account', 'AccountController@create');
Route::post('/account/login', 'AccountController@login');

Route::group(['middleware' => ['account.auth']], function () {
    Route::get('/account/{accountGUID}', 'AccountController@read');
    Route::put('/account/{accountGUID}', 'AccountController@update');
    Route::post('/account/{accountGUID}/verify', 'AccountController@update'); // @todo
    Route::put('/account/{accountGUID}/password', 'AccountController@changePassword');
    Route::delete('/account/{accountGUID}', 'AccountController@delete');
});


