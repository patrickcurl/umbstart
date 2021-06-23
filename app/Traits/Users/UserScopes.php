<?php

namespace App\Traits\Users;

use Bouncer;

trait UserScopes
{
    public function scopeOrderByName($query)
    {
        $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeWithName($query, $name)
    {
        $name = $name ?? '';

        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeWithEmail($query, $email)
    {
        if ($email === null || $email === '') {
            return false;
        }

        return $query->where('email', '=', $email);
    }

    public function scopeByTeam($query)
    {
        $team    = auth()->user()->activeTeam;
        $userIds = $team->users->pluck('id')->all();
        $query->whereIn('id', $userIds);
    }

    /**
     * Constrain the given query by the provided role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function constrainWhereIs($query, $roles)
    {
        $roles = is_array($roles) ? $roles : array_slice(func_get_args(), 1);

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        });
    }

    /**
     * Constrain the given query by the provided role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $role
     * @return void
     */
    public function scopeWhereIs($query, $role)
    {
        call_user_func_array(
            [new static, 'constrainWhereIs'],
            func_get_args()
        );
    }

    public function scopeFilter($query, array $filters)
    {
        $user = auth()->user();
        $query->when(!$user, function ($query) use ($filters) {
            $query->whereIn('id', []);
        }, function ($query) use ($user, $filters) {
            $search  = $filters['search'] ?? null;
            $trashed = $filters['trashed'] ?? false;
            $roles   = $filter['roles'] ?? [];
            $query->when($user->isNotAn('admin'), function ($query) use ($user) {
                $query->whereIn('id', $user->teamUserIds);
            })->when(!empty($roles), function ($query) use ($roles) {
                $query->whereIs($roles);
            })->when(!empty($trashed), function ($query) use ($trashed) {
                if ($trashed === 'with') {
                    $query->withTrashed();
                }

                if ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            })->when(!empty($search), function ($query) use ($search) {
                $query->search($search);
            });
        });

        if ($user) {
            try {
                // $query->when(!empty($user), function ($query) use ($user) {


                // $query->when($user->isNotAn('admin'), function ($query, $userIds) {
                    //     $query->whereIn('id', $userIds);
                    // })->when(!empty($roles), function ($query) use ($roles) {
                    //     $query->whereIs($roles);
                    // })->when(!empty($trashed), function ($query, $trashed) {
                    //     if ($trashed === 'with') {
                    //         $query->withTrashed();
                    //     }

                    //     if ($trashed === 'only') {
                    //         $query->onlyTrashed();
                    //     }
                    // })->when($search, function ($query, $search) {
                    //     $query->search($search);
                    // });
                // });
            } catch (\Exception $e) {
                \Log::info($e->getMessage() . ' on ' . $e->getLine());
            }
        } else {
            $query->whereIn('id', []);
        }
    }
}
