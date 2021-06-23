<?php

declare(strict_types=1);
namespace App\Models;

use App\Traits\Teams\TeamTrait;

class Team extends BaseModel
{
    use TeamTrait;

    protected $table = 'teams';

    protected $fillable = ['name', 'slug', 'logo', 'owner_id'];

    public function teamOwned($relationship)
    {
        return $this->morphedByMany($relationship, 'teamable');
    }

    public function scopeByUser($query)
    {
        $teamIds = auth()->user()->teams->pluck('id')->toArray();
        $query->whereIn('id', $teamIds);
    }

    public function scopeFilter($query, array $filters)
    {
        $search   = $filters['search'] ?? [];
        $trashed  = $filters['trashed'] ?? '';
        $includes = $filters['includes'] ?? [];
        $query->when(!empty($search), function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        })->when(!empty($trashed), function ($query) use ($trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when(!empty($includes), function ($query) use ($includes) {
            foreach ($includes as $included) {
                $query->with($included);
            }
        });
    }
}
