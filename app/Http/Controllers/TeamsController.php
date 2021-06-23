<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class TeamsController extends Controller
{
    public function index()
    {
        return Inertia::render('Teams/Index', [
            'filters'       => Request::all('search', 'trashed'),
            'teams'         => Auth::user()->teams()
                ->filter(Request::only('search', 'trashed'))
                ->orderBy('name')
                ->get()
                ->transform(function ($team) {
                    return [
                        'id'          => $team->id,
                        'name'        => $team->name,
                        'owner'       => $team->owner->name,
                        'owner_email' => $team->owner->email,
                        'is_owner'    => auth()->user()->id === $team->owner->id,
                        'deleted_at'  => $team->deleted_at
                    ];
                })
        ]);
    }

    public function create()
    {
        return Inertia::render('Teams/Create');
    }

    public function store()
    {
        Auth::user()->teams()->create(
            Request::validate([
                'name'         => ['required', 'max:100'],
                'display_name' => ['nullable', 'max:80'],
                'description'  => ['nullable'],
            ])
        );

        return Redirect::route('teams')->with('success', 'Team created.');
    }

    public function edit(Team $team)
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

    public function update(Team $team)
    {
        $team->update(
            Request::validate([
                'name'         => ['required', 'max:100'],
                'display_name' => ['nullable', 'max:80'],
                'description'  => ['nullable'],
            ])
        );

        return Redirect::route('teams.edit', $team)->with('success', 'Team updated.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return Redirect::route('teams.edit', $team)->with('success', 'Team deleted.');
    }

    public function restore(Team $team)
    {
        $team->restore();

        return Redirect::route('teams.edit', $team)->with('success', 'Team restored.');
    }
}
