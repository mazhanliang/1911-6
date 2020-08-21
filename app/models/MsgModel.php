<?php
/**
 * Created by PhpStorm.
 * User: 冀文杰
 * Date: 2020/8/19
 * Time: 9:24
 */
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class MsgModel extends Model{
    public $table = 'news_msg';

    public $primaryKey = 'msg_id';

    public $timestamps = false;
}