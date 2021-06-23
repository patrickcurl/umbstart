<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tags = !empty($this->message) && !empty($this->message->tags) ? (new TagCollection($this->message->tags)) : [];

        return [
            'id'          => $this->id,
            'owner'       => optional($this->team->owner)->name ?? '',
            'owner_email' => optional($this->team->owner)->email ?? '',
            'message'     => !empty($this->message) ? [
                'id'      => $this->message->id,
                'subject' => $this->message->subject,
            ] : [],
            'tags'        => $tags,
            'is_owner'    => optional($this->team->owner)->id === auth()->user()->id ? true : false,
            'src'         => $image->getUrl() ?? '',
            'deleted_at'  => $image->deleted_at ?? ''
        ];
    }
}
