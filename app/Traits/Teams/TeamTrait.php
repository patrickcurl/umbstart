<?php

declare(strict_types=1);
namespace App\Traits\Teams;

/**
 * This file is part of Teamwork
 *
 * @license MIT
 * @package Teamwork
 */

use App\Traits\HasSlug;
use App\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

trait TeamTrait
{
    use HasSlug, SoftDeletes, ValidatingTrait;

    public static function bootTeamTrait()
    {
        static::saved(function ($model) {
            if (!$model->users->contains($model->owner)) {
                $model->users()->attach($model->owner);
            }

            if (!$model->owner) {
                if (auth()->user()) {
                    $model->update(['owner_id' => auth()->user()->id]);
                }
            }
        });
    }

    // /**
    //  * One-to-Many relation with the invite model
    //  * @return mixed
    //  */
    // public function invites()
    // {
    //     return $this->hasMany(\App\Models\Invite::class, 'team_id', 'id');
    // }

    public function invites()
    {
        return $this->morphToMany('App\Models\Team', 'inviteable');
    }

    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'team_users', 'team_id', 'user_id')->withTimestamps();
    }

    /**
     * Has-One relation with the user model.
     * This indicates the owner of the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'owner_id');
    }

    public function isOwner()
    {
        return Auth::user()->id === $this->owner->id;
    }

    /**
     * Helper function to determine if a user is part
     * of this team.
     *
     * @param Model $user
     * @return bool
     */
    public function hasUser(Model $user)
    {
        return $this->users->contains('id', $user->id);
    }

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @param Model $user
     *
     * @return Model
     */
    public function addUser(Model $user): Model
    {
        $exists = $this
            ->users()
            ->where('team_users.user_id', $user->id)
            ->exists();

        if (!$exists) {
            $this->users()->attach($user);
        }

        return $user->refresh();
    }

    /**
     * @param Model $user
     *
     * @return Model
     */
    public function removeUser(Model $user): Model
    {
        $exists = $this
            ->users()
            ->where('team_users.user_id', $user->id)
            ->exists();

        if ($exists) {
            $this->users()->detach($user);
        }

        return $user->refresh();
    }

    /**
     * @param Model $user
     *
     * @return Model
     */
    public function joinTeam(Model $user): Model
    {
        $this->teams()->attach($user);

        return $user->refresh();
    }

    public static function createTeam(array $data, $owner_id) : Model
    {
        $data['owner_id'] = $owner_id;
        $team             = self::create($team);
        $team->attach($owner_id);
    }

    /**
     * @param Model $team
     *
     * @return Model
     */
    public function leaveTeam(Model $team): Model
    {
        $exists = $this
            ->teams()
            ->where('team_users.team_id', $team->id)
            ->exists();

        if ($exists) {
            $this->teams()->detach($team);
        }

        return $team->refresh();
    }

    /**
    * @return bool
    */
    public function inTeam(Model $team): bool
    {
        return $this
            ->teams()
            ->where('team_users.team_id', $team->id)
            ->exists();
    }
}
