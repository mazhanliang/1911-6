<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CateModel extends Model
{
    public $table='news_cate';
    public $primaryKey='cate_id';
    public $timestamp=false;

}
