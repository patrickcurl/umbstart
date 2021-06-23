<?php

declare(strict_types=1);
namespace App\Models;

use App\Traits\Users\UserTrait;
use Bouncer;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'remember_token', 'current_team_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * the validation rules
     *
     * @var array
     */
    public $rules = [];

    public $asYouType = true;

    /**
     * get validation rules
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->rules;
    }

    public function getTeams()
    {
        $owned = $this->teams->where('owner_id', $this->id)->map(function ($t) {
            return [
                'name'  => $t->name,
                'slug'  => $t->slug,
                'logo'  => $t->logo,
                'owner' => !empty($t->owner) ? [
                    'id'    => $t->id,
                    'name'  => $t->name,
                    'email' => $t->email,
                ] : [],
            ];
        })->toArray();
        $member_of = $this->teams->where('owner_id', '<>', $this->id)->map(function ($t) {
            return [
                'name'  => $t->name,
                'slug'  => $t->slug,
                'logo'  => $t->logo,
                'owner' => !empty($t->owner) ? [
                    'id'    => $t->id,
                    'name'  => $t->name,
                    'email' => $t->email,
                ] : [],
            ];
        })->toArray();

        return [
            'owned'     => $owned,
            'member_of' => $member_of
        ];
    }

    /**
     * Get users that another user has access to via
     * being their team owners.
     */
    public function getTeamUserIdsAttribute()
    {
        $teamsOwned = $this->ownedTeams()->pluck('id')->toArray();

        return DB::table('team_users')->whereIn('team_id', $teamsOwned)->get()->pluck('user_id')->unique()->values()->toArray();
    }

    public function mailboxes()
    {
        return $this->hasMany(Mailbox::class);
    }

    public function claimInvite($code = null)
    {
        if (empty($code)) {
            throw new Exception('Invite code is required.');
        }

        $invite = Invite::where('code', $code)->first();
        $team   = $invite->team;
        $this->teams()->attach($team);

        return $team;
    }
}
