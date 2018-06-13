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

Auth::routes();

Route::get('/', 'GalleryController@index');

Route::get('/admin', 'AdminController@index')->name('admin');
Route::post('/admin', 'AdminController@update');

Route::get('/gallery', 'GalleryController@index')->name('gallery');
Route::get('/gallery/more', 'GalleryController@more');
