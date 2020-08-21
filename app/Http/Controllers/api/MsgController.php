<?php

namespace App\Http\Controllers\api;

use App\Exceptions\ApiException;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\models\MsgModel;
use Illuminate\Http\Request;

class MsgController extends CommonController
{

//    //
    public $msg_expire = 60*1;
////
////
    public function sendMsgCode(Request $request)
    {


        $sid = $this->checkApiParam('sid');
        $phone = $this->checkApiParam('phone');
        $img_code = $this->checkApiParam('user_img_code');
        $type = $this->checkApiParam('type');

        //验证图片验证码是否正确
        $request->session()->setId($sid);

        $request->session()->start();

        $session_code = $request->session()->get('img_code');

        if ($session_code != $img_code) {
            throw new ApiException('图片验证码不正确');
        }
       else{
           $request -> session() -> forget('img_code');
        }

//        //验证手机号是否存在
//        if($this -> checkUserExists( $phone ) > 0){
//            throw new ApiException('你的账号以及注册过了,不能再注册');
//        }

        //验证手机号是否存在
        //$this ->checkUserExists();

        //发送短信验证码
        $where = [
            ['phone', '=', $phone],
            ['type', '=', $type],
        ];

        $msg_model = new MsgModel();

        $obj = $msg_model->where($where)->orderBy('msg_id', 'desc')->first();
        $msg_code = rand(100000, 999999);

        if (!empty($obj) && $obj->expire > time()) {
            //throw new ApiException('短信验证码发送太频繁,请稍后再试');
        }
        //判断是否超过10条数据
        $time = strtotime(date('Y-m-d') . '00:00:00');
        $count_where = [
            ['phone', '=', $phone],
            ['ctime', '>', $time]
        ];
        if ($msg_model->where($count_where)->count() >= 10) {
            //throw  new ApiException('今天发送的太多,明天再试');
        }

        //把数据写入数据库中
        $msg_model->phone = $phone;
        $msg_model->type = $type;
        $msg_model->msg_code = $msg_code;
        $msg_model->expire = time() + $this->msg_expire;
        $msg_model->status = 1;
        $msg_model->ctime = time();
        if ($msg_model->save()) {
            //发送短信
            if ($this->sendAliMsgCode($phone, $msg_code)) {
                return $this->success();
            } else {
                throw new ApiException('发送失败，请重试');
            }
        }
    }
   }
