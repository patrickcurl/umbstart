<?php

declare(strict_types=1);
namespace App\Traits\Users;

/**
 * This file is part of Teamwork
 *
 * @license MIT
 * @package Teamwork
 */

use App\Models\User;
use App\Traits\Teams\Inviteable;
use App\Traits\Users\UserHasTeams;
use App\Traits\Users\UserScopes;
use App\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Scout\Searchable;
use Silber\Bouncer\Database\Concerns\HasAbilities;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Sofa\Eloquence\Eloquence;

trait UserTrait
{
    use Inviteable,
        UserScopes,
        UserHasTeams,
        // Searchable,

        Eloquence,
        ValidatingTrait,
        SoftDeletes,
        Notifiable,
        Authorizable,
        HasRolesAndAbilities {
            UserScopes::scopeWhereIs insteadof HasRolesAndAbilities;
        }

    protected $searchableColumns = [
        'first_name',
        'last_name',
        'email',
        'teams.name',
        'teams.slug',
    ];

    public function isImpersonating()
    {
        return session()->has('impersonating');
    }

    /**
    * Get the indexable data array for the model.
    *
    * @return array
    */
    public function toSearchableArray()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->firstname . $this->lastname,
            'email' => $this->email,
            'teams' => $this->teams()->select(['name', 'slug'])->get()->toJson()
        ];
    }

    public function getNameAttribute()
    {
        $name = implode(' ', [$this->first_name ?? '', $this->last_name ?? '']);

        return ucwords($name);
    }

    /**
     * hashes password
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
}
