<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Team::class, function (Faker $faker) {
    $company = $faker->company;

    return [
        'name'         => $company,
        'slug'         => $faker->slug,
        'logo'         => $faker->imageUrl($width = 640, $height = 480),
    ];
});
