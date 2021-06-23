<?php

namespace App\Http\Actions\Images;

use App\Models\Message;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Index extends Controller
{
    public function __invoke($kw = null)
    {
        $team = auth()->user()->activeTeam;

        if (!empty($team)) {
            $messages = !empty($kw) ? Message::search($kw)->where('team_id', $team->id) : Message::where('team_id', $team->id);
            $messages->whereHas('images');
        } else {
            if (auth()->isAn('admin')) {
                !empty($kw) ? Message::search($kw) : (new Message)->newQuery();
            }
        }

        return Inertia::render('Images/Index', [
            'filters' => Request::all('search', 'trashed'),
            'images'  => $messages->paginate()
                ->transform(function ($message) use ($team) {
                    return [
                        'id'          => $message->id,
                        'owner'       => optional($team->owner)->name ?? '',
                        'owner_email' => optional($team->owner)->email ?? '',
                        'message'     => $message->message ? [
                            'id'      => $message->message->id,
                            'subject' => $message->message->subject,
                        ] : [],
                        'tags' => $message && $message->tags ? $message->tags->transform(function ($tag) {
                            return [
                                'id'   => $tag->id,
                                'name' => $tag->name,
                            ];
                        }) : [],
                        'is_owner'   => $team && !empty($team->owner) && !empty($team->owner->id) ? auth()->user()->id === $team->owner->id : false,
                        'src'        => optional($message->images)->first()->getUrl() ?? '',
                        'deleted_at' => optional($message)->deleted_at ?? '',
                    ];
                }),
        ]);
    }
}
