<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'read_at' => $this->read_at ? $this->read_at->timestamp : null,
            'created_at' => $this->created_at->timestamp,
            'data' => $this->data,
        ];
    }
}
