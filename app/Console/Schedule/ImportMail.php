<?php

namespace App\Console\Schedule;

use App\Models\Mailbox;
use App\Tasks\Mail\FetchMails;
use Carbon\Carbon;

class ImportMail
{
    public function __invoke()
    {
        $mailboxes = Mailbox::get();

        foreach ($mailboxes as $mailbox) {
            if ($mailbox->imported_at === null || $mailbox->imported_at->gt(Carbon::now()->subDay())) {
                $fetcher = new FetchMails($mailbox);
                $fetcher->run();
            }
        }
    }
}
