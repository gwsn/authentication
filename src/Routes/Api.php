<?php

Route::post('/account', 'AccountController@create');

Route::group(['middleware' => ['authentication']], function () {
    Route::get('/account/{accountGUID}', 'AccountController@read');
    Route::put('/account/{accountGUID}', 'AccountController@update');
    Route::delete('/account/{accountGUID}', 'AccountController@delete');
});


