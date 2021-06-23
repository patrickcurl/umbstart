<?php

namespace App\Traits\Teams;

trait Inviteable
{
    public function invitesReceived()
    {
        return $this->belongsToMany(Invite::class, 'receiver_id');
    }

    public function invitesSent()
    {
        return $this->belongsToMany(Invite::class, 'sender_id');
    }

    public function teamSentInvites()
    {
        return $this->hasManyThrough(
            Team::class,
            Invite::class,
            'user_id',
            'team_id',
            'id',
            'id'
        );
    }
}
