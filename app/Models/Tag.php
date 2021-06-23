<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ValidatingTrait;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Tag extends BaseModel
{
    use ValidatingTrait;
    // use CacheableEloquent;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'weight',
        'team_id',
        'user_id',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'group'      => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    public function messages()
    {
        return $this->morphedBy('App\Models\Message', 'taggable');
    }

    /**
    * Morph to the tag
    *
    * @return \Illuminate\Database\Eloquent\Relations\MorphTo
    */
    public function taggable()
    {
        return $this->morphTo();
    }

    public function tagged()
    {
        return $this->hasMany(Tagged::class, 'tag_id', 'id');
    }

    public static function report($teamId = null, array $order = [])
    {
        $key = !empty($order['key']) ? $order['key'] : 'weight';
        $dir = !empty($order['dir']) ? $order['dir'] : 'DESC';
        // $sql = "
        //     SELECT * FROM (
        //         SELECT DISTINCT ON(t.tag_id)
        //         't.tag_id',
        //         't.weight',
        //         't.team_id',
        //         'tag.name',
        //         'team.name as team_name'
        //         FROM 'taggables t'
        //         JOIN tags as tag on tag.id = t.tag_id
        //         LEFT JOIN teams team on team.id = t.team_id
        //         WHERE t.team_id = {$teamId}
        //         ORDER BY t.tag_id
        //     )
        //     ORDER BY
        //         {$key} {$dir}
        // ";

        // return DB::select(DB::raw($sql))->paginate(1);
        return DB::table('taggables as t')
            ->select(
                't.tag_id',
                't.weight',
                't.team_id',
                'tag.name',
                'team.name as team_name'
            )
            ->join('tags as tag', 'tag.id', '=', 't.tag_id')
            ->leftJoin('teams as team', 'team.id', '=', 't.team_id')
            ->where('t.team_id', $teamId ?? auth()->user()->active_team_id)
            ->orderBy(@$key, @$dir)
            ->paginate(1);
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('tags');
        $this->setRules([
            'name'        => 'required|string|max:150',
            // 'weight'      => 'nullable|numeric|max:10000000',
        ]);
    }

    /**
     * Get first tag(s) by name or create if not exists.
     *
     * @param mixed       $tags
     * @param string|null $group
     *
     * @return \Illuminate\Support\Collection
     */
    public static function findByNameOrCreate($tags, $taggables): Collection
    {
        return collect($tags)->map(function (string $tag) use ($taggables) {
            return static::firstByName($tag, $taggables) ?: static::createByName($tag, $taggables);
        });
    }

    /**
     * Find tag by name.
     *
     * @param mixed       $tags
     * @param string|null $group
     *
     * @return \Illuminate\Support\Collection
     */
    public static function findByName($tags): Collection
    {
        return collect($tags)->map(function (string $tag) {
            return ($exists = static::firstByName($tag)) ? $exists->getKey() : null;
        })->filter()->unique();
    }

    /**
     * Get first tag by name.
     *
     * @param string      $tag
     * @param string|null $group
     *
     * @return static|null
     */
    public static function firstByName(string $tag, array $taggables)
    {
        $activeTeamId = auth()->user()->active_team_id;
        $queryData = [
            'name'    => $tag,
            'team_id' => $activeTeamId
        ];

        if (!empty($taggables)) {
            $queryData = array_merge($queryData, $taggables);
        }

        return static::query()->where($queryData)->first();
    }

    /**
     * Create tag by name.
     *
     * @param string      $tag
     * @param string|null $group
     *
     * @return static
     */
    public static function createByName($tag, array $taggables = []): self
    {
        $teamId = auth()->user()->active_team_id;
        $userId = auth()->user()->id;

        if (empty($teamId)) {
            throw new Exception('Active team not set.');
        }

        if (empty($userId)) {
            throw new Exception('User not found.');
        }

        $data = [
            'name'    => $tag,
            'user_id' => $userId,
            'team_id' => $teamId
        ];

        if (!empty($taggables)) {
            $data = array_merge($data, $taggables);
        }

        return static::create($data);
    }

    public function scopeWithType(Builder $query, string $type = null): Builder
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type)->ordered();
    }

    public function scopeContaining(Builder $query, string $name, $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();

        return $query->whereRaw('lower(' . $this->getQuery()->getGrammar()->wrap('name->' . $locale) . ') like ?', ['%' . mb_strtolower($name) . '%']);
    }

    /**
     * @param string|array|\ArrayAccess $values
     * @param string|null $type
     * @param string|null $locale
     *
     * @return \Spatie\Tags\Tag|static
     */
    public static function findOrCreate($values, string $type = null, string $locale = null)
    {
        $tags = collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $type, $locale);
        });

        return is_string($values) ? $tags->first() : $tags;
    }

    public static function getWithType(string $type): DbCollection
    {
        return static::withType($type)->ordered()->get();
    }

    public static function findFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()
            ->where("name->{$locale}", $name)
            ->where('type', $type)
            ->first();
    }

    public static function findFromStringOfAnyType(string $name, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()
            ->where("name->{$locale}", $name)
            ->first();
    }

    protected static function findOrCreateFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $tag = static::findFromString($name, $type, $locale);

        if (!$tag) {
            $tag = static::create([
                'name' => [$locale => $name],
                'type' => $type,
            ]);
        }

        return $tag;
    }

    public function setAttribute($key, $value)
    {
        if ($key === 'name' && !is_array($value)) {
            // return $this->setTranslation($key, app()->getLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }
}
