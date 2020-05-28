<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('permissions')->default('[]');
            $table->timestamps();
        });

        $roles = [
            [
                "name" => "developer",
                "permissions" => [
                    "*" => true,
                ],
            ],
            [
                "name" => "admin",
                "permissions" => [
                    "*" => true,
                ],
            ],
            [
                "name" => "mediabuyer",
                "permissions" => [
                ],
            ],
            [
                "name" => "affiliate",
                "permissions" => [
                ],
            ],
            [
                "name" => "advertiser",
                "permissions" => [
                ],
            ],
            [
                "name" => "accountant",
                "permissions" => [
                ],
            ],
        ];

        foreach($roles as $role) {
            DB::table('roles')->insert(
                array(
                    'name' => $role['name'],
                    'permissions' => json_encode($role['permissions']),
                )
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
