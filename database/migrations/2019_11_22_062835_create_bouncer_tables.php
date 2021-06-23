<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBouncerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('only_owned')->default(false);
            $table->json('options')->nullable();
            $table->mediumInteger('scope')->nullable()->index();
            $table->timestamps();
        });

        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('level')->nullable();
            $table->mediumInteger('scope')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
            $table->unique(
                ['name', 'scope'],
                'roles_name_unique'
            );
        });

        Schema::create('assigned_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id')->index();
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');
            $table->unsignedBigInteger('restricted_to_id')->nullable();
            $table->string('restricted_to_type')->nullable();
            $table->mediumInteger('scope')->nullable()->index();
            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'assigned_roles_entity_index'
            );
            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ability_id')->index();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('forbidden')->default(false);
            $table->mediumInteger('scope')->nullable()->index();
            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'permissions_entity_index'
            );
            $table->foreign('ability_id')
                  ->references('id')->on('abilities')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('assigned_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('abilities');
    }
}
