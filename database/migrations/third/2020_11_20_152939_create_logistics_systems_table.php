<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticsSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_logistics_systems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('api')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // TODO: I should make a seeder
        DB::table('third_logistics_systems')->insert(
            array(
                'name' => 'momoka',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_logistics_systems');
    }
}
