<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserTokenModel extends Model
{
    public $table='news_user_token';
    public $primaryKey='id';
    public $timestamps = false;
}
