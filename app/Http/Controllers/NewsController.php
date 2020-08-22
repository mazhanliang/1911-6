<?php

namespace App\Http\Controllers;

use App\Model\CateModel;
use App\Model\NewsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class NewsController extends CommonController
{
    /**
     * 新闻列表接口
     */
    public function newslist(Request $request){
        #拼接pahe和pagesize参数
        $page=$request->post('page')??1;
        $page_size=$request->post('page_size')??10;
        #拼接一下缓存的key
        $page_key='index_list_'.$page;
        $page_key.='_'.$this->getCacheVersion('news');

        if($id_list=Redis::get($page_key)){

            $id_arr=unserialize($id_list);
            $list=$this->getListCache($id_arr);
            return $this->success($list);
        }

        $news_model=new NewsModel();
        #只查询已发布的数据
        $where=[
            ['status','=',3]
        ];
        #按照发布时间倒叙
        $order_field='publish_time';
        $order_type='desc';
        $news_model=new NewsModel();
        $news_list_obj=NewsModel::with('getCate')
            ->where($where)
            ->orderBy($order_field,$order_type)
            ->paginate($page_size);
        if(!empty($news_list_obj)){
            foreach($news_list_obj as $k=>$v){
                $v->news_image =env('IMG_HOST').$v->news_image;
            }
        }

        $news_list=collect($news_list_obj)->toArray();
        #根据列表的数据生成 原子的缓存 按照详情数据缓存
        if(!empty($news_list)){
            $this->buildNewsDeatilCache($news_list['data']);
        }
        #把查询出来的数据生成缓存 存入redis中
        $this->buildNewsListCache($page_key,$news_list['data']);
        return $this->success($news_list['data']);

    }

    public function buildNewsListCache($page_key,$news_list){
        $id_arr=array_column($news_list,'news_id');
        if( Redis::set($page_key,serialize($id_arr))){
            Redis::expire($page_key,60*5);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 根据列表的数据 生成详情的缓存
     *
     */
    public function buildNewsDeatilCache($news_list){

        foreach($news_list as $k=>$v){
            $v['cate_name']=$v['get_cate']['cate_name'];
            $detail_key='news_detail_'. $v['news_id'];
            Redis::hMset($detail_key,$v);
            Redis::expire($detail_key,60*5);
        }
        return true;
    }


    public function getListCache($id_arr){
        $all=[];
        foreach($id_arr as $k => $v){
            $detail_key='news_detail_'.$v;
            $detail= Redis::hGetAll($detail_key);
            if(empty($detail)){
                $detail_obj=NewsModel::with('getCate')->find($v);
                $detail_obj->cate_name=$detail_obj->getCate->cate_name;
                $detail=collect($detail_obj)->toArray();
                Redis::hMset($detail_key,$detail);
                $all[]=$detail;
            }else{
                $all[]=$detail;
            }
        }
        return $all;
    }


    /**
     * @return mixed     分类接口首页展示
     */
            public function test(){
                $data=CateModel::get();
                $datas=json_decode($data,true);
                return $this->success($datas);
            }

    /**
     * 新闻详情
     */
    public function details(){
        $news_id=request()->post('news_id');
        $where=[
            ['status','=',3],
            ['news_id','=',$news_id]
        ];
        $news_details=NewsModel::with('getCate')->where($where)->first();
        return $this->success($news_details);
    }


    /**
     * 热卖查询新闻
     */
    public function remai(){
        $where=[
            ['status','=',3],
        ];
        $news_details=NewsModel::with('getCate')->where($where)->orderBy('click_count','desc')->paginate(10);
        $news_details_list=collect($news_details)->toArray();
        return $this->success($news_details_list['data']);
    }
}
