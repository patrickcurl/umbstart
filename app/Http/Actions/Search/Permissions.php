<?php

namespace App\Http\Actions\Search;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Permissions extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Users/Index', [
            'users' => User::byRole(request()->role)
                    ->orderByName()
                    ->filter(Request::only('search', 'role', 'trashed'))
                    ->get()
                    ->transform(function ($user) {
                        return [
                            'id'         => $user->id,
                            'name'       => $user->name,
                            'email'      => $user->email,
                            'deleted_at' => $user->deleted_at,
                        ];
                    })
        ]);
    }
}
