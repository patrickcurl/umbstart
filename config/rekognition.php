<?php

return [
    'credentials' => [
        'key'    => env('AWS_ACCESS_KEY'),
        'secret' => env('AWS_SECRET_KEY'),
    ],
    'region'  => 'us-east-1',
    'version' => 'latest'
];
