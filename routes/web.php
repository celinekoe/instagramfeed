<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();

// Authentication Routes...
Route::get('login', [
    'as' => 'login',
    'uses' => 'Auth\LoginController@showLoginForm'
]);
Route::post('login', [
    'as' => '',
    'uses' => 'Auth\LoginController@login'
]);
Route::post('logout', [
    'as' => 'logout',
    'uses' => 'Auth\LoginController@logout'
]);

// Password Reset Routes...
// Route::post('password/email', [
//     'as' => 'password.email',
//     'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail'
// ]);
// Route::get('password/reset', [
//     'as' => 'password.request',
//     'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm'
// ]);
// Route::post('password/reset', [
//     'as' => '',
//     'uses' => 'Auth\ResetPasswordController@reset'
// ]);
// Route::get('password/reset/{token}', [
//     'as' => 'password.reset',
//     'uses' => 'Auth\ResetPasswordController@showResetForm'
// ]);

// Registration Routes...
// Route::get('register', [
//     'as' => 'register',
//     'uses' => 'Auth\RegisterController@showRegistrationForm'
// ]);
// Route::post('register', [
//     'as' => '',
//     'uses' => 'Auth\RegisterController@register'
// ]);
    
Route::get('/', 'AdminController@index');

Route::get('/admin', 'AdminController@index')->name('admin');
Route::get('/admin/more', 'AdminController@more');
Route::get('/admin/refresh', 'AdminController@refresh');
Route::post('/admin', 'AdminController@update');

Route::get('/gallery', 'GalleryController@index')->name('gallery');
Route::get('/gallery/more', 'GalleryController@more');

Route::get('/joinmailinglist', 'JoinMailingListController@index');
Route::post('/joinmailinglist', 'JoinMailingListController@update');
