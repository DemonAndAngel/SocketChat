<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->integer('from_user_id')->unsigned();
            $table->foreign('from_user_id')->references('id')->on('users');
            $table->integer('to_user_id')->unsigned();
            $table->foreign('to_user_id')->references('id')->on('users');
            $table->integer('is_friend')->default(0);//发送好友请求0，成为好友1
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
        Schema::dropIfExists('friends');
    }
}
