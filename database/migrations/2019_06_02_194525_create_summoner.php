<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummoner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summoners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('profileIconId');
            $table->string('name');
            $table->string('puuid');
            $table->bigInteger('summonerLevel');
            $table->bigInteger('revisionDate');
            $table->string('accountId');
            $table->string('summonerId');
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summoner');
    }
}
