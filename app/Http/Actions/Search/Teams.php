<?php

namespace App\Http\Actions\Search;

use App\Models\Team;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Teams extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Teams/Index', [
            'teams' => Team::byUser()
                    ->filter(Request::only('search', 'role', 'trashed', 'includes'))
                    ->get()
                    ->sortBy('name')
                    ->transform(function ($user) {
                        return [
                            'id'             => $team->id,
                            'name'           => $team->name,
                            'owner'          => $team->owner->name,
                            'is_owner'       => $team->owner_id === auth()->user()->id,
                            'is_active_team' => $team->id === auth()->user()->active_team_id ? true : false,
                            'deleted_at'     => $team->deleted_at,
                        ];
                    })
        ]);
    }
}
