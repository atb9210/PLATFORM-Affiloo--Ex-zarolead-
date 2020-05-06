<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('network');
            $table->integer('product_id')->unsigned();
            $table->string('quantity')->nullable();
            $table->decimal('price', 9, 2)->default(0.0);
            $table->decimal('commission', 9, 2)->default(0.0);
            $table->decimal('sub_commission', 9, 2)->default(0.0);
            $table->decimal('total_commission', 9, 2)->default(0.0);
            $table->integer('traffic_source_id')->unsigned();
            $table->json('info')->nullable();
            $table->integer('visits')->unsigned()->default(0);
            $table->integer('leads')->unsigned()->default(0);
            $table->decimal('total_cost', 9, 2)->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('forms');
    }
}
