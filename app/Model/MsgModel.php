<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MsgModel extends Model
{
    public $table='news_msg';
    public $primaryKey='msg_id';
    public $timestamp=false;
}
