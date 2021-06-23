<?php

namespace App\Http\Actions\Mailboxes;

use App\Http\Requests\MailboxRequest;
use App\Models\Mailbox;
use App\Services\Mailer;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Store extends Controller
{
    public function __invoke(MailboxRequest $request)
    {
        $request->validated();
        $mailbox    = new Mailbox;
        $attributes = ['username', 'password', 'host', 'port', 'encryption', 'validate_cert'];

        foreach ($attributes as $attribute) {
            $mailbox->$attribute = $request->$attribute;
        }
        $mailbox->user_id = auth()->user()->id;

        $mailer          = new Mailer($mailbox);

        try {
            if ($mailbox->canConnect()) {
                $mailbox->active = 1;
            }
        } catch (\Exception $e) {
            $mailbox->active            = 0;
            $mailbox->last_error_status = 'failed_to_connect';
        }

        $mailbox->save();

        return redirect()->route('mailboxes.index');
    }
}
