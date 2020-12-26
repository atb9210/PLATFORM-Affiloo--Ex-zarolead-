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
            $table->string('name')->unique();
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
                        "viewAny" => true,
                    ],
                    "flow" => [
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
                        "viewCommission" => true,
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
                    "traffic_source" => [
                        "viewAny" => true,
                    ],
                    "network" => [
                        "viewAny" => true,
                    ],
                    "product_media" => [
                        "viewAny" => true,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "ad" => [
                        "*" => true,
                    ],
                    "domain" => [
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
                    "order" => [
                        "viewAny" => false,
                        "create" => false,
                        "update" => false,
                        "delete" => false,
                    ],
                    "product" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => false,
                        "delete" => false,
                    ],
                    "tag" => [
                        "viewAny" => true,
                    ],
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
                        "viewAny" => true,
                    ],
                    "flow" => [
                        "viewAny" => false,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "form" => [
                        "viewAny" => false,
                        "create" => false,
                        "update" => false,
                        "delete" => false,
                        "viewCommission" => true,
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
                    "traffic_source" => [
                        "viewAny" => true,
                    ],
                    "network" => [
                        "viewAny" => true,
                    ],
                    "product_media" => [
                        "viewAny" => true,
                        "create" => true,
                        "update" => true,
                        "delete" => true,
                    ],
                    "ad" => [
                        "*" => true,
                    ],
                    "domain" => [
                        "*" => true,
                    ],
                ],
            ],
            [
                "name" => "account_manager",
                "permissions" => [
                    "order" => [
                        "viewAny" => true,
                    ],
                    "user" => [
                        "viewAny" => true,
                    ],
                    "product" => [
                        "viewAny" => true,
                    ],
                    "tag" => [
                        "viewAny" => true,
                    ],
                    "traffic_source" => [
                        "viewAny" => true,
                    ],
                    "network" => [
                        "viewAny" => true,
                    ],
                    "product_media" => [
                        "*" => true,
                    ],
                    "ad" => [
                        "*" => true,
                    ],
                    "domain" => [
                        "*" => true,
                    ],
                    "invoice" => [
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
