<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->enum('type', ['redirect', 'hotload']);
            $table->json('rules')->nullable();
            $table->bigInteger('traffic_source_id')->unsigned();
            $table->json('domains')->nullable();
            $table->string('path_name');
            $table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('traffic_source_id')->references('id')->on('traffic_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flows');
    }
}
