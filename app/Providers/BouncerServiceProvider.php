<?php

namespace App\Providers;

use Auth;
use Bouncer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class BouncerServiceProvider extends ServiceProvider
{
    protected $user;

    public function __construct()
    {

        // parent::__construct($app);
        // $this->user = auth()->user() ?? null;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->user = auth()->user();

        if (!empty($this->user)) {
            Bouncer::cache();
            Bouncer::useAbilityModel(\App\Models\Ability::class);
            Bouncer::useRoleModel(\App\Models\Role::class);

            if (!empty($this->user->activeTeam())) {
                Bouncer::scope()->to($this->user->active_team_id);
            }
            Bouncer::ownedVia(\App\Models\Mailbox::class, function ($mailbox) {
                return $this->isTeamOwner() && $this->userTeamsHave($mailbox);
            });

            // Bouncer::ownedVia(\App\Models\Message::class, function(){
            //     return $message->
            // });

            // Bouncer::ownedVia(\App\Models\Tag::class, function ($tag) {
            //     return $this->userOwns($tag)
            // });

            Bouncer::ownedVia(\App\Models\Project::class, function ($project) {
                return $this->userOwns($project);
            });

            Bouncer::ownedVia(\App\Models\Invite::class, function ($invite) {
                return in_array($this->user->id, [@$invite->sender_id, @$invite->receiver_id]);
            });
        }
    }

    protected function userOwnsThrough($parent, $child, $parent_key) : bool
    {
        $ownsParent = $this->user->id === @$parent->user_id;

        if (!$ownsParent) {
            $ownsParent = $this->user->teams->contains('id', @$parent->team_id);
        }

        if ($ownsParent === true) {
            return $child->$parent_key = $parent->id;
        }

        return $false;
    }

    protected function isTeamOwner()
    {
        return $this->user->activeTeam()->isOwner();
    }
}
