<?php

Route::resource('/', 'SiteController');
Route::resource('site', 'SiteController');
Route::resource('site.page', 'PageController');

// Overwrite routes from plugin
Route::group(['prefix' => 'auth'], function() {
	Route::get('login', ['as' => 'auth.login', 'uses' => '\App\Http\Controllers\AuthController@getLogin']);
	Route::post('login', ['as' => 'auth.postlogin', 'uses' => '\App\Http\Controllers\AuthController@postLogin']);
	Route::get('logout', ['as' => 'auth.logout', 'uses' => '\App\Http\Controllers\AuthController@getLogout']);
	Route::get('loggedout', ['as' => 'auth.loggedout', 'uses' => '\App\Http\Controllers\AuthController@getLoggedout']);
});

Route::get('/{catchall?}', function() {
	return response()->view('inline');
})->where('catchall', '(.*)');