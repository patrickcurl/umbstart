<?php

namespace App\Http\Actions\Impersonate;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;

class Start extends Controller
{
    use AuthorizesRequests;

    public function __invoke(User $user)
    {
        if ($user->can('impersonate-users')) {
            session()->put('impersonating', $user->id);
        }

        return Redirect::route('users');
        // return [
        //     'message' => __('Impersonating :user', ['user' => $user->name]),
        // ];
    }
}
