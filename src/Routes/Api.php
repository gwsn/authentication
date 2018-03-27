<?php

Route::post('/account', 'AccountController@create');

Route::group(['middleware' => ['authentication']], function () {
    Route::get('/account/{accountID}', 'AccountController@read');
    Route::put('/account/{accountID}', 'AccountController@update');
    Route::delete('/account/{accountID}', 'AccountController@delete');
});


