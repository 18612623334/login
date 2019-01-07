<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('token',100)->default('')->comment('凭证');
            $table->char('phone',20)->unique()->comment('手机号码');
            $table->string('password', 100)->default('')->comment('密码');
            $table->string('nickname', 50)->default('')->comment('昵称');
            $table->string('header', 255)->default('')->comment('头像');
            $table->tinyInteger('sex')->default(0)->comment('性别：1=男，2=女');
            $table->integer('login_error_number')->default(0)->comment('登录错误次数');
            $table->timestamp('login_error_date')->default('0000-00-00 00:00:00')->comment('最后登录错误时间');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `users` comment'用户表'");
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
