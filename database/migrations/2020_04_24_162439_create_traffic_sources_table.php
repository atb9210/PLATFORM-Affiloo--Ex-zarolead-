<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrafficSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('custom_params')->default('[]');
            $table->timestamps();
            $table->boolean('archived')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traffic_sources');
    }
}
