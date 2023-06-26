<?php

Route::post('/', 'AuthController@authenticate');

Route::post('logout', 'AuthController@logout')->middleware(['api-auth']);
