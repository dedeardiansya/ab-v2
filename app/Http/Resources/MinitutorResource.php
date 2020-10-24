<?php

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\Playlist;
use Illuminate\Http\Resources\Json\JsonResource;

class MinitutorResource extends JsonResource
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
            'active' => $this->active,
            'last_education' => $this->last_education,
            'university' => $this->university,
            'city_and_country_of_study' => $this->city_and_country_of_study,
            'majors' => $this->majors,
            'interest_talent' => $this->interest_talent,
            'contact' => $this->contact,
            'expectation' => $this->expectation,
            'reason' => $this->reason,
            'followers' => UsersResource::collection($this->subscribers),
            'playlists' => PostsResource::collection(Playlist::postListQuery($this->playlists())->get()),
            'articles' => PostsResource::collection(Article::postListQuery($this->articles())->get()),
            'created_at' => $this->created_at ? $this->created_at->timestamp : null,
            'updated_at' => $this->updated_at ? $this->updated_at->timestamp : null,
        ];
    }
}
