<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait ChecksAttribute
{
    protected function attributeExists($attribute)
    {
        return array_key_exists($attribute, $this->attributes);
    }
}
