<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Concerns\IsRole;

class Role extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'title', 'level'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'    => 'int',
        'level' => 'int',
    ];
}
