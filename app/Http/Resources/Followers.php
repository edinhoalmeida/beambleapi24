<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *   schema="followers",
     * @OA\Property(
     *   property="name",
     *   type="string",
     *   description="Name of follower"
     * ),
     * @OA\Property(
     *   property="image",
     *   type="string",
     *   description="Photo of follower"
     * ),
     * @OA\Property(
     *   property="city_country",
     *   type="string",
     *   description="City, country of follower"
     * ),
 * ),
 */
class Followers extends ResourceCollection
{

    public function toArray($request)
    {
        if(empty($this->total)){
            $this->total = 0;
        }
        if(empty($this->this_user_follow)){
            $this->this_user_follow = 0;
        }
        $dados = [
            'total' => $this->total,
            'this_user_follow' => $this->this_user_follow,
        ];
        return $dados;
    }
}
