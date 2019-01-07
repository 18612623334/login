<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTripartitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tripartites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->tinyInteger('type')->default(0)->comment('类型：1=微信，2=QQ，3=微博');
            $table->string('open_id', 100)->default('')->comment('第三方标识ID');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `user_tripartites` comment'第三方登录'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tripartites');
    }
}
