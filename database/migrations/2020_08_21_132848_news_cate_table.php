<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewsCateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('news_cate', function (Blueprint $table) {
            //设置表引擎
            $table -> engine ='InnoDB';
            //字符集
            $table -> charset ='utf8';
            //校队
            $table -> collation ='utf8_unicode_ci';

            $table = int('cate_id')->comment('分类id,主键');
            $table = varchar('cate_name',20);
            $table = tinyint('status',4);
            $table = unsignedInteger('ctime');
            $table = unsignedInteger('ntime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
