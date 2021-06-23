<?php

declare(strict_types=1);
namespace App\Models;

use App\Services\Rekog;
use DB;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Intervention\Image\ImageManagerStatic as Image;
use Laravel\Scout\Searchable;
use Sofa\Eloquence\Eloquence;
use Spatie\MediaLibrary\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use Eloquence;

    protected $casts = [
        'manipulations'     => 'array',
        'custom_properties' => 'array',
        'responsive_images' => 'array',
    ];

    protected $searchableColumns = [
        'message.subject',
        'name'
    ];

    // use Searchable;

    // protected $touches = ['Message'];

    // protected $hidden = [
    //     'searchable',
    // ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->user_id = auth()->user()->id ?? null;
            $model->team_id =  auth()->user()->active_team_id ?? null;
        });
    }

    /*
     * Get the full url to a original media file.
    */
    public function getFullUrl(string $conversionName = ''): string
    {
        return url($this->getUrl($conversionName));
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    // // public function keywords()
    // // {
    // //     return $this->hasMany(Keyword::class);
    // // }

    // public function keywords()
    // {
    //     return $this->belongsToMany(Keyword::class, 'message_keywords', 'media_id', 'message_id');
    // }

    public function detectLabels()
    {
        $rekog  = new Rekog();
        $image  = $this->getBase64();

        if (!$image) {
            return false;
        }
        $result = $rekog->getTags($image);
        $data   = [];

        foreach ($result->get('Labels') as $label) {
            \Log::info('confidence: ' . $label['Confidence']);
            $weight = bcdiv((string) $label['Confidence'], '100', 4);
            $data[] = [
                'name'          => $label['Name'],
                'weight'        => (string) $weight,
                'taggable_type' => 'App\Models\Message',
                'taggable_id'   => $this->message->id,
                'team_id'       => auth()->user()->activeTeam->id,
                'user_id'       => auth()->user()->id
            ];
            \Log::info('weight: ' . $weight);
        }


        $message         = $this->message;

        foreach ($data as $d) {
            Tag::create($d);
        }
        // DB::table()
        // DB::table('tags')->insert($data);
        // $message->labels = $data;
        // $message->save();
    }

    public function getBase64()
    {
        try {
            $path     = $this->getPath();
            $ext      = pathinfo($path, PATHINFO_EXTENSION);

            if ($ext === 'gif') {
                $newPath = storage_path('tmp') . '/tempImage.jpg';
                $image   = Image::make($path)->encode('jpg')->save($newPath);
                $path    = $newPath;
            }
            // return (string) Image::make($path)->encode('data-url');
            // if(strpos(strtolower($path), 'gif')){
            //     Image::make()
            // }
            $fp_image = fopen($path, 'r');
            $base64   = fread($fp_image, filesize($path));
            fclose($fp_image);

            return $base64 ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    // public function searchableAs()
    // {
    //     return 'media_index';
    // }

    // public function toSearchableArray()
    // {
    //     // $message         = $this->message;
    //     // $data            = $this->toArray();
    //     // $data            = $this->transform($data);
    //     $message = $this->message;

    //     return [
    //         'subject'    => $message->subject,
    //         'from'       => $message->from[0]['full'],
    //         'to'         => $message->to[0]['full'],
    //         'content'    => $message->text_body,
    //         'tags'       => collect($message->labels)->pluck('name')->implode(' '),
    //     ];
    // }

    // public function searchableOptions()
    // {
    //     return [
    //         // You may wish to change the default name of the column
    //         // that holds parsed documents
    //         // 'column' => 'indexable',
    //         // You may want to store the index outside of the Model table
    //         // In that case let the engine know by setting this parameter to true.
    //         'external' => true,
    //         // If you don't want scout to maintain the index for you
    //         // You can turn it off either for a Model or globally
    //         'maintain_index' => true,
    //         // Ranking groups that will be assigned to fields
    //         // when document is being parsed.
    //         // Available groups: A, B, C and D.
    //         'rank' => [
    //             'fields' => [
    //                 'tags'    => 'A',
    //                 'subject' => 'D',
    //                 'from'    => 'D',
    //                 'to'      => 'D',
    //                 'content' => 'C'
    //             ],
    //             // Ranking weights for searches.
    //             // [D-weight, C-weight, B-weight, A-weight].
    //             // Default [0.1, 0.2, 0.4, 1.0].
    //             'weights' => [0.1, 0.2, 0.4, 1.0],
    //             // Ranking function [ts_rank | ts_rank_cd]. Default ts_rank.
    //             'function' => 'ts_rank',
    //             // Normalization index. Default 0.
    //             'normalization' => 32,
    //         ],
    //         // You can explicitly specify a PostgreSQL text search configuration for the model.
    //         // Use \dF in psql to see all available configurationsin your database.
    //         'config' => 'simple',
    //     ];
    // }

    // public static function searchTest()
    // {
    //     return self::whereHas('message', function ($q) {
    //         $q->where(\DB::raw('"labels->name" LIKE "%Pos%"'));
    //     })->get();
    // }
}
