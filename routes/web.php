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

//展示图片验证码
Route::any('showImageCode','api\BlogController@showImageCode');

//获取图片验证码
Route::any('getImgUrl','api\BlogController@getImageCodeUrl');

//发送短信验证码
Route::any('sendMsgCode','api\MsgController@sendMsgCode');

//注册接口111
Route::any('reg','api\UserController@reg');

