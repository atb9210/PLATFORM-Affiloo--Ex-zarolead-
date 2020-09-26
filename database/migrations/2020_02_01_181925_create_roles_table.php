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
            $table->json('permissions')->nullable();
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
                    "order" => [
                        "*" => true,
                    ],
                    "product" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "flows" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "form" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "landing" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "tag" => [
                        "viewAny" => true,
                    ],
                    "advertiser" => [
                        "viewAny" => true,
                    ],
                    "traffic_source" => [
                        "viewAny" => true,
                    ],
                    "campaign" => [
                        "viewAny" => true,
                    ],
                    "network" => [
                        "viewAny" => true,
                    ],
                    "ad" => [
                        "*" => true,
                    ],
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
            [
                "name" => "buyer",
                "permissions" => [
                    "order" => [
                        "*" => false,
                    ],
                    "product" => [
                        "*" => false,
                    ],
                    "flows" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "form" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "landing" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "tag" => [
                        "viewAny" => true,
                    ],
                    "advertiser" => [
                        "viewAny" => true,
                    ],
                    "traffic_source" => [
                        "viewAny" => true,
                    ],
                    "campaign" => [
                        "viewAny" => true,
                    ],
                    "network" => [
                        "viewAny" => true,
                    ],
                    "ad" => [
                        "*" => true,
                    ],
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
