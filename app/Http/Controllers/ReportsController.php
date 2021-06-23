<?php

declare(strict_types = 1);
namespace App\Http\Controllers;

use App\Models\Tag;
use DB;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        $activeTeamId = auth()->user()->active_team_id;
        // $sql = "SELECT tag.name, "
        // $tagIds         = DB::table('tags')->where('team_id', $activeTeamId)->where('taggable_type', 'App\Models\Message')->pluck('id')->toArray();
        // $tags           = Tag::whereIn('id', $tagIds)->get();
        $tags = Tag::report($activeTeamId);

        return Inertia::render(
            'Reports/Index',
            [
                'tags' => $tags->transform(
                    function ($tag) {
                        return [
                            'id'     => $tag->tag_id,
                            'name'   => $tag->name,
                            'weight' => $tag->weight,
                            'team'   => $tag->team_name,
                            'count'  => DB::table('taggables')->where(
                                [
                                    'team_id' => $tag->team_id,
                                    'tag_id'  => $tag->tag_id,
                                ]
                            )->count(),
                        ];
                    }
                ),
            ]
        );
    }
}
