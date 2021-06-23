<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\User;
use Bouncer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users'   => User::orderByName()
                ->filter(Request::only('search', 'role', 'trashed'))
                ->get()
                ->transform(function ($user) {
                    return [
                        'id'         => $user->id,
                        'name'       => ucwords($user->name),
                        'email'      => $user->email,
                        'deleted_at' => $user->deleted_at,
                    ];
                }),
            'permissions' => [
                'impersonate' => Bouncer::can('impersonate-users'),
                'delete'      => Bouncer::can('delete-users'),
                'list'        => Bouncer::can('list-users'),
                'edit'        => Bouncer::can('edit-users')
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        Request::validate([
            'first_name'      => ['required', 'max:50'],
            'last_name'       => ['required', 'max:50'],
            'email'           => ['required', 'max:50', 'email', Rule::unique('users')],
            'password'        => ['nullable'],
            'current_team_id' => ['sometimes', 'exists:teams:id']
        ]);

        User::create([
            'first_name' => Request::get('first_name'),
            'last_name'  => Request::get('last_name'),
            'email'      => Request::get('email'),
            'password'   => Request::get('password'),
        ]);

        return Redirect::route('users')->with('success', 'User created.');
    }

    public function edit(User $user) : \Inertia\Response
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(User $user)
    {
        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name'  => ['required', 'max:50'],
            'email'      => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password'   => ['nullable'],
        ]);

        $user->update(Request::only('first_name', 'last_name', 'email', 'owner'));

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::route('users.edit', $user)->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return Redirect::route('users.edit', $user)->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return Redirect::route('users.edit', $user)->with('success', 'User restored.');
    }
}
