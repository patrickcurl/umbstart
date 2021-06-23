<?php

namespace App\Http\Actions\Mailboxes;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Create extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Mailboxes/Create');
    }
}
