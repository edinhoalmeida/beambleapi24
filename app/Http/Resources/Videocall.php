<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\User;

/**
 * @OA\Schema(
 *   schema="videocall",
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
     *   property="other_id",
     *   type="integer",
     * ),
     * @OA\Property(
     *   property="other_name",
     *   type="string",
     * ),
     * @OA\Property(
     *   property="other_location",
     *   type="string",
     * ),
     * @OA\Property(
     *   property="other_company_type",
     *   type="string",
     * ),
     * @OA\Property(
     *   property="status",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="meeting_id",
     *   type="string",
     * ),
      * @OA\Property(
     *   property="meeting_object",
     *   type="object",
     *   ref="#/components/schemas/meetingobject",
     * ),
     * @OA\Property(
     *   property="beamer_agree_at",
     *   type="string",
     *   nullable=True
     * ),
  * ),
 */
class Videocall extends JsonResource
{

    private  $logged_userid = null;
    private  $other_user = null;

    public function __construct($resource, $logged_userid = null)
    {
        $this->resource = $resource;
        $this->logged_userid = $logged_userid;
    }

    public function toArray($request)
    {
        if(empty($this->meeting_object)){
            $meeting_object = [];
        } else {
            $meeting_object = json_decode($this->meeting_object);
        }

        $other_id = $other_name = $other_location = $other_company_type = null;
        if($this->logged_userid){

            if($this->client_id==$this->logged_userid){
                $this->other_user = User::find($this->beamer_id);
            }
            if($this->beamer_id==$this->logged_userid){
                $this->other_user = User::find($this->client_id);
            }

            $address = $this->other_user->some_address();
            if(!empty($address->city)){
                $other_location = $address->city . ", " . $address->country;
            }
            $other_id = $this->other_user->id;
            $other_name = $this->other_user->name;
            $other_company_type = $this->other_user->company_type;
        }
        $dados = [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'beamer_id' => $this->beamer_id,
            'other_id' => $other_id,
            'other_name' => $other_name,
            'other_location' => $other_location,
            'other_company_type' => $other_company_type,
            // 'with_donation' => $this->with_donation,
            // 'is_freemium' => $this->is_freemium,
            'status' => $this->status,
            'meeting_id' => $this->meeting_id,
            'meeting_object' => $meeting_object,
            'beamer_agree_at' => $this->beamer_agree_at,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at
        ];
        return $dados;
    }
}
