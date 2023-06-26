<?php

Route::get('permissions', 'PermissionController@get');
Route::get('permissions/{id}', 'PermissionController@getById');

Route::get('permission-categories', 'PermissionCategoryController@get');
