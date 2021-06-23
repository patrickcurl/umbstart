<?php

namespace App\Http\Actions\Teams;

use Illuminate\Routing\Controller;

use Inertia\Inertia;

class Create extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Teams/Create');
    }
}
