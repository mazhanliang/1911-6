<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    public $table='news_user';
    public $primaryKey='user_id';
    public $timestamp=false;
}
