<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\models\UserModel;
use App\Http\Controllers\Controller;
use App\Model\UserToken;

use Illuminate\Support\Facades\Redis;
class  CommonController extends Controller
{

        public function success($data = [], $status = 200, $msg = 'success')
        {
                return [
                    'status' => $status,
                    'msg' => $msg,
                    'data' => $data
                ];
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
    /**
     * @param string $cache_type
     * @return
     * 获取缓存的版本号
     */
    public function getCacheVersion($cache_type='news'){
        switch($cache_type){
            case 'news':
                $cache_version_key='news_cache_version';
                $version=Redis::get($cache_version_key);
                break;
            default;
                break;
        }
        if(empty($version)){
            Redis::set($cache_version_key,1);
            $version=1;
        }
        return $version;
    }
}

