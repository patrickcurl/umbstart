<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Spatie\Sluggable\HasSlug as BaseHasSlug;
use Spatie\Sluggable\SlugOptions;

trait HasSlug
{
    use BaseHasSlug;

    protected $generateSlugsFrom = 'name';

    protected $saveSlugsTo = 'slug';

    protected $slugMaxLength = 60;

    protected $useSlugMaxLength = true;

    protected $generateOnUpdate = false;

    protected $slugSeparator = '-';

    protected $allowDuplicateSlugs = false;

    /**
     * Boot the trait.
     */
    protected static function bootHasSlug()
    {
        // Auto generate slugs early before validation
        static::validating(function (Model $model) {
            if ($model->exists && $model->getSlugOptions()->generateSlugsOnUpdate) {
                $model->generateSlugOnUpdate();
            } elseif (! $model->exists && $model->getSlugOptions()->generateSlugsOnCreate) {
                $model->generateSlugOnCreate();
            }
        });
        // Route::bind(str_singular($this->getTable()), function ($value) {
        //     return self::where('slug', $value)->orWhere('id', $value)->first();
        // });
    }

    public function resolveRouteBinding($value)
    {
        if (!is_numeric($value)) {
            return $this->where('slug', $value)->first();
        }

        return $this->where('id', $value)->first() ?? abort(404);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        $options = SlugOptions::create()
            ->generateSlugsFrom($this->generateSlugsFrom)
            ->saveSlugsTo($this->saveSlugsTo);

        if (!empty($useSlugMaxLength) && $useSlugMaxLength === true) {
            $options->slugsShouldBeNoLongerThan($this->slugMaxLength ?? 60);
        }

        if ($this->generateOnUpdate === false) {
            $options->doNotGenerateSlugsOnUpdate();
        }

        $options->usingSeparator($this->slugSeparator);

        if ($this->allowDuplicateSlugs) {
            $options->allowDuplicateSlugs();
        }

        return $options;
    }

    // *
    //  * Get the route key for the model.
    //  *
    //  * @return string

    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }
}
