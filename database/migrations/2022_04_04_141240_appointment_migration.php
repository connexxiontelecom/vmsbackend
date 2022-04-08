<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->String('name')->nullable(false);

            $table->String('phone')->nullable(false);

            $table->String('purpose')->nullable(false);

            $table->integer('host')->nullable(false);

            $table->string('date')->nullable(false);

            $table->string('time')->nullable(false);

            $table->integer('status')->nullable();// 2 approved //3 pending //4 finished //5 declined

            $table->rememberToken();
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
        Schema::dropIfExists('appointments');
    }
};
