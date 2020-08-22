<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            #设置表引型
            $table->engine = 'InnoDB';
            #字符集
            $table->charset = 'utf8';
            #校对
            $table->collation ='utf8_unicode_ci';

            $table->increments('user_id','10')->comment('用户id，主键');
            $table->string('nick_name','20')->comment('用户的昵称');
            $table->string('user_name','20')->comment('用户的名字');
            $table->char('phone','11')->comment('手机号');
            $table->string('email','50')->comment('邮箱');
            $table->char('password','32')->comment('密码');
            $table->char('rand_code','6')->comment('随机码');
            $table->char('error_count','6')->comment('累计错误次数');
            $table->tinyInteger('last_error_time','4')->comment('最后一次错误时间');
            $table->unsignedBigInteger('last_login_ip','20')->comment('登陆的ip');
            $table->tinyInteger('status','4')->comment('1 待审核  2锁定   3正常   4已删除');
            $table->unsignedInteger('ctime','10')->comment('创建时间');
            $table->unsignedInteger('utime','10')->comment('修改时间');
            $table->string('head_img','200')->comment('用户的头像');
            $table->unsignedBigInteger('age')->comment('年龄');
            $table->tinyInteger('reg_type')->comment('1 pc  2 h5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
