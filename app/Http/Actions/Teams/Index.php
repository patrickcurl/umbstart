<?php

namespace App\Http\Actions\Teams;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Index extends Controller
{
    public function __invoke()
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
}
