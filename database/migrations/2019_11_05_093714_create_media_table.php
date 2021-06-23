<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->json('source')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('message_id')->nullable()->references('id')->on('messages');
            $table->unsignedBigInteger('mailbox_id')->nullable()->references('id')->on('mailboxes');
            $table->unsignedBigInteger('user_id')->nullable()->references('id')->on('users');
            $table->unsignedBigInteger('team_id')->nullable()->references('id')->on('teams');
            $table->softDeletes();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
