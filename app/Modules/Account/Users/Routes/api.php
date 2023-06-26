<?php

Route::get('', 'UserController@get');
Route::get('me', 'UserController@getAuthenticatedUser');
Route::get('{id}', 'UserController@getById');
Route::post('', 'UserController@store');
Route::put('{id}', 'UserController@update');
Route::put('{id}/restore', 'UserController@restore');
Route::delete('{id}', 'UserController@destroy');
