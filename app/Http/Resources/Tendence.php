<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class Tendence extends JsonResource
{

    public function toArray($request)
    {
        $dados = [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'surname' => $this->user->surname,
            'email' => $this->user->email,
            'city' => $this->city,
            'country' => $this->country,
            'beamer_type' => 'classic',
            'beamer_cost' => '10 €',
        ];
        return $dados;
    }
}
