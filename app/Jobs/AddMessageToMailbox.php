<?php

namespace App\Jobs;

use App\Jobs\ImageTagger;
use App\Models\Mailbox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddMessageToMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mailbox;

    protected $messageData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Mailbox $mailbox, $messageData)
    {
        $this->mailbox     = $mailbox;
        $this->messageData = $messageData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data    = collect($this->messageData);
        $pk      = $data->only('uid', 'mailbox_id', 'subject')->toArray();
        $rest    = $data->except(['uid', 'mailbox_id', 'subject'])->toArray();
        $message = $this->mailbox->messages()->firstOrCreate($pk, $rest);
        ImageTagger::dispatch($message);
    }
}
