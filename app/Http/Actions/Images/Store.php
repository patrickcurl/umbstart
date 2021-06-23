<?php

namespace App\Http\Actions\Images;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Validator;

class Store extends Controller
{
    public function __invoke()
    {
        $v = Validator::make(request()->only(['media_id', 'tag', 'weight']), [
            'media_id' => 'required|exists:media',
            'tag'      => 'required|string',
            'weight'   => 'required|numeric'
        ]);
        $media = Media::find($media_id);

        if (!$media) {
            return Redirect::route('images.index')->with('error', 'Media not found');
        }
        $activeTeamId = auth()->user()->active_team_id;

        if (empty($activeTeamId)) {
            return Redirect::route('images.index')->with('error', 'Active team not set, please choose set your active team/organization.');
        }

        if ($activeTeamId !== $media->team_id) {
            return Redirect::route('images.index')->with('error', "Media not owned by user's active team");
        }
        $tag = Tag::createByName($request->input('tag'), [
            'taggable_id'   => $media->id,
            'taggable_type' => 'App\Models\Media',
            'weight'        => $request->input('weight') ?? 0.25
        ]);

        return Redirect::route('images.index')->with('success', 'Image created.');
    }
}
