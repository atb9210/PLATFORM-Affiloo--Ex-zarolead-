<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->date('day');
            $table->integer('leads')->unsigned()->default(0);
            $table->decimal('commission', 9, 2)->default(0.0);
            $table->decimal('cost', 9, 2)->default(0.0);
            $table->decimal('extra_cost', 9, 2)->default(0.0);
            $table->decimal('profit', 9, 2)->default(0.0);
            $table->text('notes')->nullable();
            $table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_reports');
    }
}
