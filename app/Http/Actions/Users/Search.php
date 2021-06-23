<?php

namespace App\Http\Actions\Users;

use App\Models\User;
use Bouncer;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Search extends Controller
{
    public function __invoke($query)
    {
        return Inertia::render('Users/Index', [
            'users' => function () {
                return User::byTeam()->search($query)
                    // ->filter(Request::only('search', 'role', 'trashed'))
                    ->get()
                    ->transform(function ($user) {
                        return [
                            'id'         => $user->id,
                            'name'       => $user->name,
                            'email'      => $user->email,
                            'teams'      => $user->getTeams(),
                            'deleted_at' => $user->deleted_at,
                        ];
                    });
            },
            'permissions' => function () {
                return [
                    'impersonate' => Bouncer::can('impersonate-users'),
                    'delete'      => Bouncer::can('delete-users'),
                    'list'        => Bouncer::can('list-users'),
                    'edit'        => Bouncer::can('edit-users')
                ];
            }
        ]);
    }
}
