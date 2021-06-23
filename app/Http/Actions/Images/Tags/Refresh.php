<?php

namespace App\Http\Actions\Images\Tags;

use App\Models\Message;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;

class Refresh extends Controller
{
    public function __invoke(Message $message)
    {
        $message->processTags();

        return redirect()->route('images.index');
    }
}
