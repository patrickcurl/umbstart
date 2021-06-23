<?php

declare(strict_types=1);
namespace App\Services;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class EloquentRepository implements MessagesRepository
{
    public function search(string $query = ''): Collection
    {
        return Message::query()
            ->where('labels->name', 'like', "%$query%")
            ->orWhere('subject', 'like', "%{$query}%")
            ->get();
    }
}
