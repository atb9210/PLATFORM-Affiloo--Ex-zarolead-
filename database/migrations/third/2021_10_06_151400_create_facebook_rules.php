<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_facebook_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('value');
            $table->enum('status', ['active', 'disabled'])->default('disabled');
            $table->enum('frequency', [
                'every_five_minutes',
                'every_ten_minutes',
                'every_fifteen_minutes',
                'every_thirty_minutes',
                'hourly',
                'every_two_hours',
                'every_three_hours',
                'daily',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_facebook_rules');
    }
}
