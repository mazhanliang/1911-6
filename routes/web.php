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
//登录接口
Route::any('login','LoginController@login');

//展示图片验证码
//Route::any('test','LoginController@test');

Route::any('newslist','NewsController@newslist');
Route::any('test','NewsController@test');
Route::any('details','NewsController@details');
Route::any('remai','NewsController@remai');
