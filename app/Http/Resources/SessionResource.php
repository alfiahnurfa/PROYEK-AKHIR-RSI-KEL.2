<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'max_attendees' => $this->max_attendees,
        ];

        // return [
        //     'id' => $this->id,
        //     'user_id' => $this->user_id,
        //     'title' => $this->title,
        //     'description' => $this->description,
        //     'created_at' => date_format($this->created_at,'Y-m-d H:i:s'), 
        //     'author' => $this->author,
        //     'writer' => $this->whenLoaded('writer'),
        //     'comments' => $this->whenLoaded('comments', function(){
        //         return collect($this->comments)->each(function ($comment){
        //             $comment->comentator;
        //             return $comment;
        //         });
        //     }),
        //     'comments_total' => $this->whenLoaded('comments', function(){
        //         return $this->comments->count();
        //     })
        // ];
    }
}
