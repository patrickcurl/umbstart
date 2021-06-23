<?php

namespace App\Http\Actions\Images\Tags;

use App\Models\Media;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Validator;

class Store extends Controller
{
    public function __invoke(Media $media)
    {
        $media->message->createTagByName(request()->input('name'));

        return redirect()->route('images.index');
    }
}
