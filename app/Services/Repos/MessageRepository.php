<?php

declare(strict_types=1);
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

interface MessagesRepository
{
    public function search(string $query = ''): Collection;
}
