<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookMonitors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_facebook_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('advertiser_id')->unsigned();
            $table->bigInteger('form_id')->unsigned();
            $table->json('campaigns')->nullable();
            $table->json('strategy')->nullable();
            $table->timestamps();

			$table->foreign('advertiser_id')->references('id')->on('third_facebook_advertisers');
			$table->foreign('form_id')->references('id')->on('forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_facebook_monitors');
    }
}
