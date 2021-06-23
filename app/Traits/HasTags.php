<?php

namespace App\Traits;

use App\Events\TagAdded;
use App\Events\TagRemoved;
use App\Models\Tag;
use App\Models\Tagged;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait HasTags
{
    /**
     * Temp storage for auto tag
     *
     * @var mixed
     * @access protected
     */
    protected $autoTagValue;

    /**
     * Track if auto tag has been manually set
     *
     * @var boolean
     * @access protected
     */
    protected $autoTagSet = false;

    /**
     * Return collection of tagged rows related to the tagged model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tags()
    {
        return $this->morphMany(Tag::class, 'taggable');
        // ->with('tag');
    }

    /**
     * Get the tag names via attribute, example $model->tag_names
     */
    public function getTagNamesAttribute(): array
    {
        return $this->tagNames();
    }

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param string|array $tagNames
     */
    public function tag($tagNames)
    {
        $tagNames = self::makeTagArray($tagNames);

        foreach ($tagNames as $tagName) {
            $this->addTag($tagName);
        }
    }

    /**
    * Return array of the tag names related to the current model
    *
    * @return array
    */
    public function tagNames(): array
    {
        return $this->tags->map(function ($item) {
            return $item->name;
        })->toArray();
    }

    /**
     * Remove the tag from this model
     *
     * @param string|array|null $tagNames (or null to remove all tags)
     */
    public function untag($tagNames = null)
    {
        if (is_null($tagNames)) {
            $tagNames = $this->tagNames();
        }
        $tagNames = self::makeTagArray($tagNames);

        foreach ($tagNames as $tagName) {
            $this->removeSingleTag($tagName);
        }

        if (static::shouldDeleteUnused()) {
            self::deleteUnusedTags();
        }
    }

    /**
    * @return string
    */
    public static function tagModelString()
    {
        return 'App\Models\Tag';
    }

    /**
     * @return string
     */
    public static function taggedModelString()
    {
        return 'App\Models\Tag';
    }

    /**
    * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
    * Does not delete tags where 'suggest' is true
    *
    * @return int
    */
    public static function deleteUnusedTags()
    {
        $model = static::tagModelString();

        return $model::deleteUnused();
    }

    /**
     * Converts input into array
     *
     * @param string|array $tagNames
     * @return array
     */
    public static function makeTagArray($tagNames)
    {
        if (is_array($tagNames) && count($tagNames) == 1) {
            $tagNames = reset($tagNames);
        }

        if (is_string($tagNames)) {
            $tagNames = explode(',', $tagNames);
        } elseif (!is_array($tagNames)) {
            $tagNames = [null];
        }
        $tagNames = array_map('trim', $tagNames);

        return array_values($tagNames);
    }

    /**
     * Replace the tags from this model
     *
     * @param string|array $tagNames
     */
    public function retag($tagNames)
    {
        $tagNames        = self::makeTagArray($tagNames);
        $currentTagNames = $this->tagNames();
        $deletions       = array_diff($currentTagNames, $tagNames);
        $additions       = array_diff($tagNames, $currentTagNames);
        $this->untag($deletions);

        foreach ($additions as $tagName) {
            $this->addTag($tagName);
        }
    }

    /**
     * Filter model to subset with the given tags
     *
     * @param Builder $query
     * @param array|string $tagNames
     * @return Builder
     */
    public function scopeWithAllTags(Builder $query, $tagNames): Builder
    {
        if (!is_array($tagNames)) {
            $tagNames = func_get_args();
            array_shift($tagNames);
        }
        $tagNames  = self::makeTagArray($tagNames);
        $className = $query->getModel()->getMorphClass();

        foreach ($tagNames as $name) {
            $model = self::taggedModelString();
            $tags  = $model::query()
                ->where('name', $name)
                ->where('taggable_type', $className)
                ->get()
                ->pluck('taggable_id');
            $primaryKey = $this->getKeyName();
            $query->whereIn($this->getTable().'.'.$primaryKey, $tags);
        }

        return $query;
    }

    /**
     * Filter model to subset with the given tags
     *
     * @param Builder $query
     * @param array|string $tagNames
     * @return Builder
     */
    public function scopeWithAnyTag(Builder $query, $tagNames): Builder
    {
        $tags = $this->assembleTagsForScoping($query, $tagNames);

        return $query->whereIn($this->getTable().'.'.$this->getKeyName(), $tags);
    }

    /**
     * Filter model to subset without the given tags
     *
     * @param Builder $query
     * @param array|string $tagNames
     * @return Builder
     */
    public function scopeWithoutTags(Builder $query, $tagNames): Builder
    {
        $tags = $this->assembleTagsForScoping($query, $tagNames);

        return $query->whereNotIn($this->getTable().'.'.$this->getKeyName(), $tags);
    }

    /**
     * Adds a single tag
     *
     * @param string $tagName
     */
    private function addTag($tagName)
    {
        $tagName = strtolower(trim($tagName));

        if (strlen($tagName) == 0) {
            return;
        }
        $previousCount = $this->tags()->where('name', '=', $tagName)->take(1)->count();

        if ($previousCount >= 1) {
            return;
        }
        $model  = self::taggedModelString();
        $tagged = new $model([
            'name' => $tagName,
        ]);
        $this->tags()->save($tagged);
        unset($this->relations['tagged']);
        event(new TagAdded($this, $tagName, $tagged));
    }

    /**
     * Removes a single tag
     *
     * @param $tagName string
     */
    private function removeSingleTag($tagName)
    {
        $tagName = trim($tagName);


        unset($this->relations['tags']); // clear the "cache"
        event(new TagRemoved($this, $tagName));
    }

    /**
     * Return an array of all of the tags that are in use by this model
     *
     * @return Collection|tags[]
     */
    public static function existingTags(): Collection
    {
        $model = self::taggedModelString();

        return $model::query()
            ->distinct()
            ->join('tags', 'tag_id', '=', 'tags.id')
            ->where('taggable_type', '=', (new static)->getMorphClass())
            ->orderBy('weight', 'DESC')
            ->get(['name as name', 'tags.weight as weight']);
    }

    /**
     * Should untag on delete
     */
    public static function untagOnDelete()
    {
        true;
    }

    /**
     * Delete tags that are not used anymore
     */
    public static function shouldDeleteUnused(): bool
    {
        return true;
    }

    /**
     * Set tag names to be set on save
     *
     * @param mixed $value Data for retag
     */
    public function setTagNamesAttribute($value)
    {
        $this->autoTagValue = $value;
        $this->autoTagSet   = true;
    }

    /**
     * AutoTag post-save hook
     *
     * Tags model based on data stored in tmp property, or untags if manually
     * set to false value
     */
    public function autoTagPostSave()
    {
        if ($this->autoTagSet) {
            if ($this->autoTagValue) {
                $this->retag($this->autoTagValue);
            } else {
                $this->untag();
            }
        }
    }

    private function assembleTagsForScoping($query, $tagNames)
    {
        if (!is_array($tagNames)) {
            $tagNames = func_get_args();
            array_shift($tagNames);
        }
        $tagNames   = self::makeTagArray($tagNames);
        $className  = $query->getModel()->getMorphClass();
        $model      = self::taggedModelString();
        $tags       = $model::query()
            ->whereIn('name', $tagNames)
            ->where('taggable_type', $className)
            ->get()
            ->pluck('taggable_id');

        return $tags;
    }
}
