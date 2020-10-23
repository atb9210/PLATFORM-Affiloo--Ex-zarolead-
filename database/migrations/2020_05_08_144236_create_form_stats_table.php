<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('form_id')->unsigned();
            $table->date('day');
            $table->integer('visits')->unsigned()->default(0);
            $table->integer('leads')->unsigned()->default(0);
            $table->decimal('total_commission', 9, 2)->default(0.0);
            $table->decimal('total_cost', 9, 2)->default(0.0);
            $table->json('info')->nullable();
            $table->json('custom_params_stats')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);

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
        Schema::dropIfExists('form_stats');
    }
}
