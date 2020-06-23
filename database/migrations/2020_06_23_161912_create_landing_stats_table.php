<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLandingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_stats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('landing_id')->unsigned();
            $table->date('day');
            $table->integer('visits')->unsigned()->default(0);
            $table->integer('leads')->unsigned()->default(0);
            $table->decimal('total_commission', 9, 2)->default(0.0);
            $table->decimal('total_cost', 9, 2)->default(0.0);
            $table->json('info')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);

			$table->foreign('landing_id')->references('id')->on('landings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_stats');
    }
}
