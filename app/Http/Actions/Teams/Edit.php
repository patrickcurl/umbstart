<?php

namespace App\Http\Actions\Teams;

use App\Models\Team;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class Edit extends Controller
{
    public function __invoke(Team $team)
    {
        return Inertia::render('Teams/Edit', [
            'team' => [
                'id'           => $team->id,
                'name'         => $team->name,
                'slug'         => $team->slug,
                'display_name' => $team->display_name,
                'description'  => $team->description,
                'deleted_at'   => $team->deleted_at,
                'users'        => $team->users()->get()->map->only('id', 'name', 'email'),
            ],
        ]);
    }
}
