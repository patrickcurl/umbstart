<?php

namespace App\Http\Actions\Images\Tags;

use App\Models\Media;
use App\Models\Message;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class Search extends Controller
{
    public function __invoke($tagName)
    {
        $team     = auth()->user()->activeTeam;
        $messages = Message::search($tagName)->where('team_id', $team->id);
        // \Log::info(request()->all());
        $images   = [];
        // foreach($messages as $message){
        //     $images[] = [
        //         'id' =>
        //     ]
        // }
        return Inertia::render('Images/Index', [
            'filters'        => Request::all('search', 'trashed'),
            'keyword'        => $tagName,
            'images'         => $messages->paginate()
                ->transform(function ($message) use ($team) {
                    $image  = $message->media->first();

                    if ($image) {
                        return [
                            'id'          => $image->id,
                            'owner'       => $team->owner->name ?? '',
                            'owner_email' => $team->owner->email ?? '',
                            'message'     => [
                                'id'      => $message->id,
                                'subject' => $message->subject,
                            ],
                            'tags' => $message->tags->transform(function ($tag) {
                                return [
                                    'id'   => $tag->id,
                                    'name' => $tag->name
                                ];
                            }),
                            'is_owner'    => auth()->user()->id === $team->owner->id,
                            'src'         => $image->getUrl(),
                            'deleted_at'  => $team->deleted_at
                        ];
                    }
                })
        ]);
    }
}
