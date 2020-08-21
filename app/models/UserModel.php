<?php
/**
 * Created by PhpStorm.
 * User: 冀文杰
 * Date: 2020/8/19
 * Time: 9:24
 */
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model{
    public $table = 'news_user';

    public $primaryKey = 'user_id';

    public $timestamps = false;
}