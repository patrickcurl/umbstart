<?php

namespace App\Http\Actions\Search;

use App\Models\Role;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Roles extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Users/Index', [
            'users' => Role::get()
                    ->transform(function ($role) {
                        return [
                            'id'         => $role->id,
                            'name'       => $role->name,
                            'email'      => $user->email,
                            'deleted_at' => $user->deleted_at,
                        ];
                    })
        ]);
    }
}
