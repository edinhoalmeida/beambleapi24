<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="follow",
     * @OA\Property(
     *   property="total",
     *   type="integer",
     *   description="Total of followers"
     * ),
     * @OA\Property(
     *   property="this_user_follow",
     *   type="boolean",
     *   description="If the currently logged in user follows that Beamer"
     * )
 * ),
 */
class Follow extends JsonResource
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
