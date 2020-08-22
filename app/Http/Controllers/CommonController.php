<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Model\UserToken;
use Illuminate\Support\Facades\Redis;

class  CommonController extends Controller
{
    public function success($data=[],$status=200,$msg='success'){
        return[
            'status'=>$status,
            'msg'=>$msg,
            'data'=>$data
        ];
    }
    public function checkUserToken(){
        $request=request();
        $user_id=$request->post('user_id');
        $token=$request->post('token');
        $tt=$request->post('tt');
        if(empty($user_id)){
            throw new ApiException('用户id不能为空');
        }
        if(empty($token)){
            throw new ApiException('token不正确');
        }

        $token_model=new UserToken();
        $where=[
            [ 'status','=',1],
            ['user_id','=',$user_id],
            ['tt','=',$tt]
        ];
        $token_obj=$token_model->where($where)->first();
        if(empty($token_obj)){
            throw new ApiException('还没有登录呢，请先登录',1000);
        }
        if($token_obj->expire<time()){
            throw new ApiException('你需要重新登录',1000);
        }
        #验证令牌的有效期[最后一次访问 2小时]
        $token_obj->expire=time()+7200;
        $token_obj->save();
        return true;
    }

    public function  checkApiParam($key){
        $request=request();
        if(empty($value=$request->post($key))){
            throw new ApiException('缺少参数'.$key);
        }
        return $value;
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
