<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;

class Rekog
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var RekognitionClient
     */
    protected $client;

    public function __construct()
    {
        $this->args = [
            'credentials' => config('rekognition.credentials'),
            'region'      => config('rekognition.region'),
            'version'     => config('rekognition.version')
        ];

        $this->client = new RekognitionClient($this->args);
    }

    public function getTags($image)
    {
        $params = [
            'Image'      => ['Bytes' => $image],
            'Attributes' => ['ALL']
        ];

        return $this->client->detectLabels($params) ?? [];
    }
}
