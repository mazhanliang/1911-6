<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Exceptions\ApiException;
use App\models\UserModel;
use App\models\MsgModel;
use Illuminate\Http\Request;

class UserController extends CommonController
{
    //
    public function reg(Request $reuqest){

        $tt = $this ->checkApiParam('tt');
        $phone = $this ->checkApiParam('phone');
        $msg_code = $this ->checkApiParam('msg_code');
        $password = $this ->checkApiParam('password');

        $preg = '/^1{1}\d{10}$/';
        if(!preg_match_all($preg, $phone)){
            throw new ApiException('手机号格式不正确');
        }
        if( $this ->checkUserExists($phone) > 0){
            throw new ApiException('手机号已存在');
        }
        $msg_model = new MsgModel();
        $where = [
            ['phone','=',$phone],
            ['type','=',1]
        ];

        $msg_obj = $msg_model ->where($where)
            ->orderby('msg_id' ,'desc')
            ->first();

        if(empty($msg_model)){
            throw new ApiException('请先发送短信验证码');
        }
        if( $msg_obj -> msg_code != $msg_code){
            throw new ApiException('验证码错误');
        }
        if( $msg_obj -> expire <time()){
            throw new ApiException('短信过期了');
        }
        //写入用户表
        $rand_code = rand(1000,9999);
        $user_model = new UserModel();
        $user_model ->phone = $phone;
        $user_model ->password = md5($password . $rand_code);
        $user_model ->reg_type = $tt;
        $user_model ->ctime = time();
        $user_model -> status = 1;

        if($user_model -> save()) {
            return $this->success();
        }else{
            throw new ApiException('注册失败,请重试');
        }
    }
}
