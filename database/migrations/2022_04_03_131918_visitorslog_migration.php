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
        Schema::create('visitorslog', function (Blueprint $table) {
            $table->id();

            $table->integer('visit_id')->nullable(false);

            $table->integer('visitor_id')->nullable(false);

            $table->integer('signed_out')->nullable(false);

            $table->timestamp("sign_out_time")->nullable();

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
        //
    }
};
