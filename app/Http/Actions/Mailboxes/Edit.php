<?php

namespace App\Http\Actions\Mailboxes;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Edit extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Mailboxes/Edit');
    }
}
