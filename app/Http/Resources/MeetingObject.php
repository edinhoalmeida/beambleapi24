<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="meetingobject",
     * @OA\Property(
     *   property="webhook",
     *   type="object",
        * @OA\Property(
        *   property="events",
        *   type="array",
        *    @OA\Items()
        * ),
     * ),
      * @OA\Property(
     *   property="disabled",
     *   type="boolean"
     * ),
      * @OA\Property(
     *   property="createdAt",
     *   type="string",
     * ),
      * @OA\Property(
     *   property="updatedAt",
     *   type="string",
     * ),
     * @OA\Property(
     *   property="roomId",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="links",
     *   type="object",
        * @OA\Property(
        *   property="get_room",
        *   type="string"
        * ),
        * @OA\Property(
        *   property="get_session",
        *   type="string"
        * ),
     * ),
     * @OA\Property(
     *   property="id",
     *   type="string"
     * ),
  * ),
 */
class MeetingObject extends JsonResource
{

    public function toArray($request)
    {
        // only to swagger doc porpouse
        return [];
    }
}
