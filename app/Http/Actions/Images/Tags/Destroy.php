<?php

namespace App\Http\Actions\Images\Tags;

use App\Models\Tag;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class Destroy extends Controller
{
    public function __invoke(Tag $tag)
    {
        $tag->delete();

        return Redirect::route('images.index')->with('success', 'Tag deleted.');
    }
}
