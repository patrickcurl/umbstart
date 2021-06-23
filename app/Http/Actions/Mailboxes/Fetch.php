<?php

namespace App\Http\Actions\Mailboxes;

use App\Models\Mailbox;
use App\Services\Mailer;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Fetch extends Controller
{
    public function __invoke(Mailbox $mailbox)
    {
        $mailer = new Mailer($mailbox);
        $mailer->fetch();

        return redirect()->route('mailboxes.index');
    }
}
