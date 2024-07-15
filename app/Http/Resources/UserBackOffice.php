<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserBackOffice extends JsonResource
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
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'client_enabled' => (int) $this->client_enabled,
            'beamer_enabled' => (int) $this->beamer_enabled,
            'shopper_enabled' => (int) $this->shopper_enabled
        ];
        return array_values($dados);
    }
}
