<?php

namespace App\Events;

use App\Model\Tagged;
use App\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class TagAdded
{
    use SerializesModels;

    /** @var Taggable|Model **/
    public $model;

    /** @var string */
    public $tagSlug;

    /** @var Tagged */
    public $tagged;

    /**
     * Create a new event instance.
     *
     * @param Taggable|Model $model
     * @param string $tagSlug
     * @param Tagged $tagged
     */
    public function __construct($model, string $tagSlug, Tagged $tagged)
    {
        $this->model   = $model;
        $this->tagSlug = $tagSlug;
        $this->tagged  = $tagged;
    }
}
