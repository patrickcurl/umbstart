<?php

namespace App\Http\Actions\Teams;

use App\Models\Team;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class Restore extends Controller
{
    public function __invoke(Team $team)
    {
        $team->restore();

        return Redirect::route('teams.edit', $team)->with('success', 'Team restored.');
    }
}
