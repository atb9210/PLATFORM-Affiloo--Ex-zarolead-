<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });

        $d = date('Y-m-d H:i:s');

        DB::table('order_statuses')->insert(
            array(
                'description' => 'Attesa',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'Conferma',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'Non risponde',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'Rifiuto',
                'created_at' => $d,
                'updated_at' => $d
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
        Schema::dropIfExists('order_statuses');
    }
}
