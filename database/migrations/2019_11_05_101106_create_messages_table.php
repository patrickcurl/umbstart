<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('uid')->nullable();
            $table->text('subject')->nullable();
            $table->jsonb('from')->default(new Expression("'{}'::jsonb"));
            $table->jsonb('to')->default(new Expression("'{}'::jsonb"));
            $table->jsonb('cc')->default(new Expression("'{}'::jsonb"));
            $table->jsonb('bcc')->default(new Expression("'{}'::jsonb"));
            $table->jsonb('attachments')->default(new Expression("'{}'::jsonb"));
            $table->jsonb('labels')->default(new Expression("'{}'::jsonb"));
            $table->text('text_body')->nullable();
            $table->text('html_body')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->unsignedBigInteger('mailbox_id')->references('id')->on('mailboxes');
            $table->unsignedBigInteger('team_id')->references('id')->on('teams');
            $table->unique(['uid', 'mailbox_id', 'subject']);
            $table->index(['uid', 'mailbox_id', 'team_id']);
            $table->index(['mailbox_id', 'team_id']);
            $table->timestamps();
        });
        // DB::statement('ALTER TABLE messages ADD searchable tsvector NULL');
        // DB::statement('CREATE INDEX messages_searchable_index ON messages USING GIN (searchable)');
        // DB::statement("CREATE INDEX idx_t_labels_full_text ON messages USING gist ((to_tsvector('English', labels::text)))");
        // DB::statement("CREATE TRIGGER ts_searchtext BEFORE INSERT OR UPDATE on messages FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger(")

        //    DB::statement("ALTER TABLE posts ADD COLUMN searchtext TSVECTOR");
        // DB::statement("UPDATE posts SET searchtext = to_tsvector('english', title || '' || text)");
        // DB::statement("CREATE INDEX searchtext_gin ON posts USING GIN(searchtext)");
        // DB::statement("CREATE TRIGGER ts_searchtext BEFORE INSERT OR UPDATE ON posts FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('searchtext', 'pg_catalog.english', 'title', 'text')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_messages');
    }
}
