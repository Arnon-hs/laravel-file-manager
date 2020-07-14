<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['auth'], 'prefix' => 'manager', 'as' => 'manager.'], function () {
    Route::group(['prefix' => 'directory', 'as' => 'directory.'], function(){
        Route::get('/', 'DirectoryController@index')->name('index');
        Route::get('/create', 'DirectoryController@create')->name('create');
        Route::post('/store', 'DirectoryController@store')->name('store');
        Route::delete('/delete', 'DirectoryController@delete')->name('delete');
    });
    Route::group(['prefix' => 'file', 'as' => 'file.'], function(){
        Route::get('/', 'FileController@index')->name('index');
        Route::get('/upload', 'FileController@upload')->name('upload');
        Route::post('/upload', 'FileController@store')->name('store');
        Route::post('/download', 'FileController@download')->name('download');
        Route::post('/show', 'FileController@show')->name('show');
        Route::post('/rename', 'FileController@rename')->name('rename');
        Route::post('/public', 'FileController@publicLink')->name('publicLink');
        Route::delete('/delete', 'FileController@delete')->name('delete');
    });
});
