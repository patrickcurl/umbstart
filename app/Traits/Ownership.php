<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait Ownership
{
    
    public function userOwns(Model $model) : bool
    {
        $ownsModel = $this->user->id === @$model->user_id ? true : false;
        return $ownModel === true ? true : $this->user->teams->contains('id', @$model->team_id);
    }

    public function teamOwns(Model $model) : bool
    {
        if ($model->team_id) {
            return $this->user->teams->contains('id', $model->team_id);
        }
        return false;
    }

    public function isTeamOwner($team_id = null)
    {
        // Check by team id;
        return $this->user->teams->where('owner')
    }
}
