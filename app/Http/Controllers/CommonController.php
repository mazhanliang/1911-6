<?php

namespace App\Http\Controllers;
use App\Exceptions\ApiException;
use App\models\UserModel;
class CommonController extends Controller
{

    public function success($data = [], $status = 200, $msg = 'success')
    {
        return [
            'status' => $status,
            'msg' => $msg,
            'data' => $data
        ];
    }

    //检测用户令牌
    public function checkUserToken()
    {
        $request = request();
        $user_id = $request->post('user_id');
        $token = $request->post('token');
        $tt = $request->post('tt');

        if (empty($user_id)) {
            throw new ApiException('用户id不能为空');
        }
        if (empty($token)) {
            throw new ApiException('token不正确');
        }
        $token_model = new UserTokenModel();
        $where = [
            ['user_id', '=', $user_id],
            ['status', '=', 1],
            ['tt', '=', $tt]
        ];

        $token_obj = $token_model->where($where)->first();
        if (empty($token_obj)) {
            throw new ApiException('还没有登陆呢，请先登录', 1000);
        }
        if ($token_obj->expire < time()) {
            throw new ApiException('你需要重新登陆', 1000);
        }

        //验证令牌有效期 最后一次访问之后 2小小时内有效
        $token_obj->expire = time() + 7200;

        $token_obj->save();
        return true;
    }

    public function checkApiParam($key)
    {
        $request = request();

        if (empty($value = $request->post($key))) {
            throw new ApiException('缺少参数' . $key);
        }
        return $value;
    }

    public function sendAliMsgCode($phone, $code)
    {
        if (env('MSG_SEND_MARK') == 0) {
            return true;
        }
        $host = "http://dingxin.market.alicloudapi.com";
        $path = "/dx/sendSms";
        $method = "POST";
        $appcode = "3cc37b3265ff40498d6651c7f1d0a63c";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "mobile=" . $phone . "&param=code%3A" . $code . "&tpl_id=TP1711063";
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $arr = json_decode(curl_exec($curl), true);
        if ($arr['return_code'] == '00000') {
            return true;
        } else {
            return false;
        }
    }

    //检测手机号是否用户注册过
    public function checkUserExists($phone)
    {
        $user_model = new UserModel();

        $where = [
            ['phone', '=', $phone],
            ['status', '<', 4]
        ];
        return $user_model->where($where)->count();
    }
}

















//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//}