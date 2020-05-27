<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('campaign_id')->unsigned();
            $table->bigInteger('form_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();
            $table->string('url');
            $table->string('country')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);

			$table->foreign('campaign_id')->references('id')->on('campaigns');
			$table->foreign('form_id')->references('id')->on('forms');
			$table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landings');
    }
}
