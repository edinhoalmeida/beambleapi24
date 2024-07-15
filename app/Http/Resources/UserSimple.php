<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="simpleuser",
     * @OA\Property(
     *   property="id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="pref_lang",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="client_enabled",
     *   type="boolean",
     *   description="Customer has already made payment and has a saved card (or other form of payment) will have: True" 
     * ),
     * @OA\Property(
     *   property="beamer_enabled",
     *   type="boolean",
     *   description="Beamer has already completed onboarding and is eligible to receive payment and will have: True" 
     * ),
     * @OA\Property(
     *   property="shopper_enabled",
     *   type="boolean",
     *   description="Beamer is able to do the Shopper model (buy products in the store) and will have: True" 
     * ),
     * @OA\Property(
     *   property="quickon_enabled",
     *   type="boolean",
     *   description="Beamer is able to do a quick online" 
     * ),
     * @OA\Property(
     *   property="client_account",
     *   type="boolean"
     * ),
     * @OA\Property(
     *   property="beamer_account",
     *   type="boolean"
     * ), 
     * @OA\Property(
     *   property="uuid",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="is_generic",
     *   type="boolean",
     *   description="If user is a generic" 
     * ),
  * ),
 */
class UserSimple extends JsonResource
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
            'pref_lang' => $this->prefLang(),
            'client_enabled' => $this->client_enabled,
            'beamer_enabled' => $this->beamer_enabled,
            'shopper_enabled' => $this->shopper_enabled,
            'quickon_enabled' => $this->quick_on,
            'beamer_account' => $this->beamer_account,
            'client_account' => $this->client_account,
            'uuid' => $this->uuid,
            'is_generic' => empty($this->is_generic) ? false : true,
        ];
        return $dados;
    }
}
