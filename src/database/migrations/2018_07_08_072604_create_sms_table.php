<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('sms', function (Blueprint $table) {
            $table->increments('id');
            $table->char('phone',20)->default('')->comment('手机号码');
            $table->integer('sms_code')->default('0')->comment('验证码');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `sms` comment'短信表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms');
    }
}
