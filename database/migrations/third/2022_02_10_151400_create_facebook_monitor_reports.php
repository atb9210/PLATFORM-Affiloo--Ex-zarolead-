<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookMonitorReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_facebook_monitor_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('monitor_id')->unsigned();
            $table->date('day');
            $table->integer('views')->unsigned()->default(0);
            $table->decimal('spent', 9, 2)->default(0.0);
            $table->integer('conversions')->unsigned()->default(0);
            $table->decimal('conversions_value', 9, 2)->default(0.0);

			$table->foreign('monitor_id')->references('id')->on('third_facebook_monitors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_facebook_monitor_reports');
    }
}
