<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128)->default('')->comment('路由名称');
            $table->string('url', 64)->default('')->comment('路由地址');
            $table->integer('naviagtion_id')->default('0')->comment('所属导航');
            $table->integer('status')->default('0')->comment('是否显示在导航栏  1:显示,0:不显示');
            $table->string('parameter',100)->default('0')->comment('路由参数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_rule');
    }
}
