<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\OrderStatus;

class CreateOrderStatusesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });

        foreach(OrderStatus::ENUM as $o) {
            DB::table('order_statuses')->insert(
                array(
                    'id' => $o['id'],
                    'description' => $o['description'],
                )
            );
        }
        return;


        DB::table('order_statuses')->insert(
            array(
                'description' => 'waiting',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'confirmed',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'not responding',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'refused',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'not sent',
                'created_at' => $d,
                'updated_at' => $d
            )
        );
        DB::table('order_statuses')->insert(
            array(
                'description' => 'not sent',
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
    public function down() {
        Schema::dropIfExists('order_statuses');
    }
}
