<?php

namespace App\Http\Controllers;
use App\Exceptions\ApiException;
use App\Http\Controllers\CommonController;
use App\Model\UserModel;
use App\Model\UserTokenModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;
class LoginController extends CommonController
{

//    public function test(){
//
//        header('Content-Type: image/png');
//
//        $im = imagecreatetruecolor(100, 30);
//
//        $white = imagecolorallocate($im, 255, 255, 255);
//        $grey = imagecolorallocate($im, 128, 128, 128);
//        $black = imagecolorallocate($im, 0, 0, 0);
//        imagefilledrectangle($im, 0, 0, 399, 29, $white);
//
//        $text = ''.rand(1000,9999);
//
//        $font = storage_path('arial.ttf');
//        $i=0;
//        while($i<strlen($text)){
//            imageline($im,rand(0,10),rand(0,25),rand(90,100),rand(10,25),$grey);
//            imagettftext($im, 20, rand(-15,15), 11+20*$i, 21, $black, $font, $text[$i]);
//            $i++;
//        }
//
//
//        imagepng($im);
//        imagedestroy($im);
//        exit;
//    }
    public function login(){

        $phone=$this->checkApiParam('phone');
        $password=$this->checkApiParam('password');
        $tt=$this->checkApiParam('tt');
        $where=[
            ['status','=',1],
            ['phone','=',$phone],
            ['password','=',$password]
        ];
        $user_model=new UserModel();
        $arr=$user_model->where($where)->first();
        $res=collect($arr)->toArray();
        //使用redis记录错误次数
        $error_key='error_count_'.$phone;
        $error_count=Redis::get($error_key);
        if($error_count>=5){
            $expire=Redis::ttl($error_key);
            if($expire<60){
                $msg=$expire.'秒';
            }else if($expire<3600){
                $minutes=intval($expire / 60);
                $msg=$minutes.'分钟';
            }else{
                $hout=intval($expire / 3600);
                $minutes=intval(($expire-3600)/60);
                $msg=$hout.'小时'.$minutes.'分钟后';
            }
            throw new ApiException('账号被锁定,'.$msg.'接触锁定');
        }
        if(!$res){
            if($error_count< 5){
                Redis::incr($error_key);
            }
            if($error_count==null || $error_count==0){
                Redis::expire($error_key,60*120);
            }
            throw new ApiException('已经输错了'.($error_count+1).'次，5次后将锁定');
        }else{
            Redis::del($error_key);
            //生成令牌
            $token=$this->_createUserToken($res['user_id'],$tt);
            $api_response=collect($res)->toArray();
            $api_response['token']=$token;
            return $this->success($api_response);
           // return $this->success($res);
        }
    }


    /**
     * @param $id
     * @param $tt给用户生成令牌
     */
    private function _createUserToken( $id,$tt){
        $token=md5(uniqid());
        $user_token_model=new UserTokenModel();
        $where=[
            ['user_id','=',$id],
            ['tt','=',$tt],
            ['expire','>',time()]
        ];
        $user_obj=$user_token_model->where($where)->first();
        if(empty($user_obj)){
            $user_token_model->user_id=$id;
            $user_token_model->tt=$tt;
            $user_token_model->token=$token;
            $user_token_model->expire=time()+7200;
            $user_token_model->status=1;
            $user_token_model->ctime=time();
            $token_result=$user_token_model->save();
        }else{
            $user_token_model->expire=time()+7200;
            $token_result=$user_obj->save();
        }
        if($token_result){
            return $token;
        }else{
            throw new ApiException('令牌产生失效，请重试');
        }
    }
}
