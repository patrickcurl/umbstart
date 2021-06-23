<?php

declare(strict_types=1);
/*
 * This file is part of Larastan.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use League\Glide\Server;

return [
    /*
    |--------------------------------------------------------------------------
    | Statics
    |--------------------------------------------------------------------------
    */
    Model::class,
    Facade::class,
    Server::class,
];
