<?php

declare(strict_types=1);
namespace App\Models;

use App\Services\Rekog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Sofa\Eloquence\Eloquence;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Message extends BaseModel implements HasMedia
{
    use HasMediaTrait, Eloquence;

    protected $casts = [
        'id'              => 'int',
        'from'            => 'array',
        'to'              => 'array',
        'cc'              => 'array',
        'bcc'             => 'array',
        'labels'          => 'array'
    ];

    protected $guarded = [];

    protected $searchableColumns = [
        'subject',
        'tags.name'
    ];

    protected $imagesToAdd = [];

    protected $currentTry = 0;

    public function getToEmailsAttribute()
    {
        $tos = [];

        foreach ($this->to as $to) {
            $tos[] = $to['full'];
        }

        return implode(', ', $tos);
    }

    public function getFromEmailsAttribute()
    {
        $froms = [];

        foreach ($this->from as $from) {
            $froms[] = $from['full'];
        }

        return implode(', ', $froms);
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
              ->width(200)
              ->height(200)
              ->sharpen(10);
        $this->addMediaConversion('greyscale')
            ->width(200)
            ->height(200)
            ->greyscale();
        $this->addMediaConversion('responsive')
            ->withResponsiveImages();
    }

    public function mailbox()
    {
        return $this->belongsTo(Mailbox::class);
    }

    // public function keywords()
    // {
    //     return $this->belongsToMany(Keyword::class, 'message_keywords', 'keyword_id', 'message_id');
    // }

    public function images()
    {
        return $this->hasMany(Media::class);
    }

    /**
    * Get all of the tag descriptions.
    */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withPivot('weight');
    }

    public function createTagByName($name, $weight = '0.25')
    {
        $data = [
            'name'          => $name,
            'weight'        => $weight,
            'taggable_type' => 'App\Models\Message',
            'taggable_id'   => $this->id,
            'team_id'       => auth()->user()->activeTeam->id,
            'user_id'       => auth()->user()->id
        ];
        \Log::info($data);
        Tag::create($data);
    }

    public function processTags()
    {
        if ($this->processed_at !== null) {
            return $this->tags->toArray();
        }

        try {
            $rekog = new Rekog();
            $image = $this->getLargestImage();

            if (!$image instanceof \App\Models\Media) {
                return null;
            }
            $base64 = $image->getBase64();

            $tags = $this->getTags($base64);
            $this->processed_at = Carbon::now();
            $this->save();
        } catch (\Exception $e) {
            slog($e);
        }

        return $tags ?? [];
    }

    /**
     * @property  $image Must be base64 encoded image
     */
    protected function getTags($image)
    {
        $rekog = new Rekog;
        $data = $rekog->getTags($image);
        $tags = [];
        $processed = [];
        $insertedTags = [];
        $defaults = [
            'taggable_type' => 'App\Models\Message',
            'taggable_id'   => $this->id,
            'team_id'       => $this->team_id,
        ];

        foreach ($data->get('Labels') as $label) {
            $tag = [];
            $name = $label['Name'];
            $confident = $label['Confidence'];
            $tag = [
                'name' => strtolower($name),
                $weight = bcdiv((string) $label['Confidence'], '100', 4)
            ];
            $tags[] = $tag;
        }
        $tags = collect($tags);

        foreach ($tags as $tag) {
            try {
                $data = $defaults;
                $name = $tag['name'];

                if (in_array($name, $insertedTags)) {
                    continue;
                }
                $weight = $tags->where('name', $name)->avg('weight');
                $mweights = $tags->where('name', $name)->pluck('weight');
                $multiplier = 0;

                foreach ($mweights as $ms) {
                    $multiplier += $ms / 10;
                }
                $data['name'] = $name;
                $weight = $weight * (1 + $multiplier);
                $insertedTags[] = $name;
                $newTag = Tag::firstOrCreate(['name' => $name]);
                $processed[] = $newTag;
                $this->tags()->save($newTag, [
                    'weight'  => $weight,
                    'team_id' => $this->team_id
                ]);
                // $processed[] = Tag::create($data);
            } catch (\Exception $e) {
                slog($e);
            }
        }

        return $processed;
    }

    public function addImageToMedia(string $image)
    {
        try {
            $media = $this
                    ->addMediaFromUrl($image)
                    ->withResponsiveImages()
                    ->toMediaCollection('email_images');
            $media->message_id = $this->id;
            $media->mailbox_id = $this->mailbox_id;
            $media->save();

            return $media;
        } catch (\Exception $e) {
            \Log::info($e->getMessage() . ' on ' . $e->getLine() . ' in ' . $e->getFile());
            $this->currentTry++;

            if (!empty($this->imagesToAdd[$this->currentTry])) {
                $this->addImageToMedia($this->imagesToAdd[$this->currentTry]);
            }
            // $this->addMedia($this);
        }
    }

    public static function getImages($html)
    {
        $images = [];
        preg_match_all('@src="([^"]+)"@', $html, $images);
        $images = array_pop($images);

        return $images;
    }

    public function getLargestImage()
    {
        $images = $this->getImages($this->html_body);

        Image::configure(['driver' => 'gd']);
        $image_array = [];
        $stopwords = ['facebook', 'twitter', 'instagram', 'youtube', 'social', 'rss feed'];
        $fsize = new \App\Services\FastImageSize\FastImageSize;
        $largest = 0;

        foreach ($images as $image) {
            $break = 0;

            foreach ($stopwords as $word) {
                if (is_string($image) && strpos(strtolower($image), strtolower($word)) !== false) {
                    $break++;
                }
            }

            if ($break === 0) {
                try {
                    $src = $image;
                    $tries = 0;

                    while ($tries < 3) {
                        try {
                            $size = $fsize->getImageSize($src);
                            $tries = 3;
                        } catch (\Exception $e) {
                            $tries++;
                        }
                    }

                    if (is_array($size)) {
                        if ($size['width'] > 62 && $size['height'] > 62) {
                            $area = $size['width'] * $size['height'];
                            $image_array[] = [
                                'src'  => $src,
                                'size' => $area
                            ];
                        }
                    }

                    unset($tries);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        $imgCollection = collect($image_array)->sortByDesc('size')->toArray();
        $this->imagesToAdd = [];

        foreach ($imgCollection as $img) {
            $this->imagesToAdd[] = $img['src'];
        }

        if (!empty(@$this->imagesToAdd[0])) {
            return $this->addImageToMedia($this->imagesToAdd[0]);
        }

        return null;
    }

    // public function toSearchableArray()
    // {
    //     // $this         = $this->message;
    //     // $data            = $this->toArray();
    //     // $data            = $this->transform($data);

    //     return [
    //         'subject'    => $this->subject,
    //         'from'       => $this->from[0]['full'],
    //         'to'         => $this->to[0]['full'],
    //         'content'    => $this->text_body,
    //         'tags'       => collect($this->labels)->pluck('name')->implode(' '),
    //     ];
    // }

    // public function searchableOptions()
    // {
    //     return [
    //         // You may wish to change the default name of the column
    //         // that holds parsed documents
    //         'column' => 'searchable',
    //         // You may want to store the index outside of the Model table
    //         // In that case let the engine know by setting this parameter to true.
    //         // 'external' => true,
    //         // If you don't want scout to maintain the index for you
    //         // You can turn it off either for a Model or globally
    //         'maintain_index' => true,
    //         // Ranking groups that will be assigned to fields
    //         // when document is being parsed.
    //         // Available groups: A, B, C and D.
    //         'rank' => [
    //             'fields' => [
    //                 'tags'    => 'A',
    //                 'subject' => 'B',
    //                 'from'    => 'C',
    //                 'content' => 'C'
    //             ],
    //             // Ranking weights for searches.
    //             // [D-weight, C-weight, B-weight, A-weight].
    //             // Default [0.1, 0.2, 0.4, 1.0].
    //             'weights' => [0.1, 0.2, 0.4, 1.0],
    //             // Ranking function [ts_rank | ts_rank_cd]. Default ts_rank.
    //             'function' => 'ts_rank_cd',
    //             // Normalization index. Default 0.
    //             'normalization' => 32,
    //         ],
    //         // You can explicitly specify a PostgreSQL text search configuration for the model.
    //         // Use \dF in psql to see all available configurationsin your database.
    //         'config' => 'simple',
    //     ];
    // }
}
