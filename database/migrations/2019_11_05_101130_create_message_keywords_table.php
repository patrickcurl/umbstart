<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('message_keywords', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->unsignedBigInteger('message_id')->references('id')->on('messages')->onDelete('cascade');
        //     $table->unsignedBigInteger('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
        //     $table->unsignedInteger('media_id')->nullable()->references('id')->on('media')->onDelete('cascade');
        //     $table->decimal('score', 15, 4)->nullable();
        //     $table->unique(['message_id', 'keyword_id']);
        //     $table->unique(['media_id', 'keyword_id']);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('message_keywords');
    }
}
