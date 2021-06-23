<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailboxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->index();
            $table->text('password');
            $table->string('host')->default('imap.gmail.com');
            $table->integer('port')->default(993);
            $table->string('encryption')->default('ssl');
            $table->boolean('validate_cert')->default(1);
            $table->json('ignored_folders')->nullable();
            $table->boolean('active')->default(0);
            $table->string('last_error_status')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->unsignedBigInteger('user_id')->references('id')->on('users')->onCascade('delete');
            $table->unsignedBigInteger('team_id')->nullable()->references('id')->on('teams')->onCascade('delete');
            $table->unique(['username', 'team_id']);
            $table->softDeletes();
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
        Schema::dropIfExists('mailboxes');
    }
}
