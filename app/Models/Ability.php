<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use Silber\Bouncer\Database\Concerns\IsAbility;

class Ability extends BaseModel implements Auditable
{
    use isAbility, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'title'];

    protected static $logAttributes = [
        'name', 'title', 'roles.name', 'roles.title', 'user.name', 'user.email',
        'updated_at', 'user.active_team_id', 'user.active_team.name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'int',
        'entity_id'  => 'int',
        'only_owned' => 'boolean',
    ];
}
