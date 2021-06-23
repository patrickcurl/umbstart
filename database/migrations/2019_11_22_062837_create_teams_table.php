<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing teams
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug', 65535)->unique();
            $table->string('logo')->nullable();
            $table->unsignedBigInteger('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('team_users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('team_id')->index();

            $table->softDeletes();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('invites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('email');
            $table->unsignedBigInteger('team_id')->references('id')->on('teams');
            $table->unsignedBigInteger('sender_id')->nullable()->references('id')->on('users');
            $table->timestamp('claimed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('inviteables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invite_id')->references('id')->on('invites');
            $table->unsignedBigInteger('inviteable_id');
            $table->string('inviteable_type');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->bigInteger('team_id')->unsigned();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
            $table->unique(['team_id', 'name']);
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('inviteables');
        Schema::dropIfExists('invites');
        Schema::dropIfExists('team_users');
    }
}
