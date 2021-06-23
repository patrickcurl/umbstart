<?php

namespace App\Http\Actions\Teams;

use App\Models\Team;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class Update extends Controller
{
    public function __invoke(Team $team)
    {
        $team->update(
            Request::validate([
                'name'         => ['required', 'max:100'],
                'slug'         => ['max:50']
            ])
        );

        return Redirect::route('teams.index')->with('success', 'Team updated.');
    }
}
