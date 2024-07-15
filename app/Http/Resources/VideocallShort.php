<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="videocallshort",
     * @OA\Property(
     *   property="id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="client_id",
     *   type="integer",
     *   nullable=True,
     * ),
     * @OA\Property(
     *   property="beamer_id",
     *   type="integer",
     * ),
      * @OA\Property(
     *   property="status",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="duration",
     *   type="integer",
     * ),
      * @OA\Property(
     *   property="billed_seconds",
     *   type="integer",
     * ),
     * @OA\Property(
     *   property="environmental_care_msg",
     *   type="string",
     * ),
  * ),
 */
class VideocallShort extends JsonResource
{

    public function toArray($request)
    {
        if(empty($this->meeting_object)){
            $meeting_object = [];
        } else {
            $meeting_object = json_decode($this->meeting_object);
        }
        $environmental_care_msg =  __('beam.emission_co2', ['attribute' => $this->kg_co2 . 'Kg']);
        $billed_seconds = 0;
        if(!empty($this->timer_start_at) && !empty($this->timer_end_at)){
            $billed_seconds =  (int) $this->timer_end_at -  (int) $this->timer_start_at;
        }
        $dados = [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'beamer_id' => $this->beamer_id,
            // 'with_donation' => $this->with_donation,
            // 'is_freemium' => $this->is_freemium,
            'status' => $this->status,
            'duration' => $this->duration,
            'billed_seconds' => $billed_seconds,
            'environmental_care_msg' => $environmental_care_msg
        ];
        return $dados;
    }
}
