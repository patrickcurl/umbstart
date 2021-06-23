<?php

namespace App\Http\Actions\Mailboxes;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Index extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Mailboxes/Index', [
            'mailboxes' => auth()
                ->user()
                ->mailboxes()
                ->paginate()
                ->transform(function ($mailbox) {
                    return [
                        'id'            => $mailbox->id,
                        'username'      => $mailbox->username,
                        'host'          => $mailbox->host,
                        'port'          => $mailbox->port,
                        'encryption'    => $mailbox->encryption,
                        'deleted_at'    => $mailbox->deleted_at,
                        'message_count' => $mailbox->messages->count() ?? 0,
                        'team'          => $mailbox->team ? [
                            'id'   => $mailbox->team->id,
                            'name' => $mailbox->team->name
                        ] : [],
                    ];
                })
        ]);
    }
}
