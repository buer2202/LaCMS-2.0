<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'Home\IndexController@index');
Route::get('category/{id}', 'Home\CategoryController@index')->name('category.index');
Route::get('document/{id}', 'Home\DocumentController@index')->name('document.index');

// Auth::routes();
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

Route::get('test', function () {

    function a()
    {
        a();

        return 'out';
    }

});
