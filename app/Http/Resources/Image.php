<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class Image extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $dados = [
            'id' => $this->id,
            'title' => $this->title,
            'url_full' => $this->url_full,
            'url_thumbnail' => $this->url_thumbnail,
        ];
        return $dados;
    }
}
