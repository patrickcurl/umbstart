<?php

namespace App\Http\Actions\Impersonate;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;

class Stop extends Controller
{
    public function __invoke()
    {
        session()->forget('impersonating');

        return Redirect::route('users');
        // return ['message' => __('Welcome Back')];
    }
}
