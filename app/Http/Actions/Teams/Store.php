<?php

namespace App\Http\Actions\Teams;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class Store extends Controller
{
    public function __invoke()
    {
        Auth::user()->teams()->create(
            Request::validate([
                'name'         => ['required', 'max:100'],
                'slug'         => ['max:50']
            ])
        );

        return Redirect::route('teams.index')->with('success', 'Team created.');
    }
}
