<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminNavigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_navigation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('navigation_name', 10)->default('')->comment('导航名称');
            $table->integer('navigation_sort')->default('0')->comment('导航排序');
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
        Schema::dropIfExists('admin_navigation');
    }
}
