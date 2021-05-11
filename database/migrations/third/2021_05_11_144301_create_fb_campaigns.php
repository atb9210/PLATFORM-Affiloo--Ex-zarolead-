<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_fb_campaigns', function (Blueprint $table) {
            $table->string('id');
            $table->bigInteger('form_id')->unsigned()->nullable();
            $table->timestamps();

            $table->primary('id');
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
        Schema::dropIfExists('third_fb_campaigns');
    }
}
