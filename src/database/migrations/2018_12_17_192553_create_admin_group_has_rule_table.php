<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminGroupHasRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_group_has_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_gid')->default(0)->comment('角色 ID');
            $table->integer('rule_rid')->default(0)->comment('权限 ID');
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
        Schema::dropIfExists('admin_group_has_rule');
    }
}
