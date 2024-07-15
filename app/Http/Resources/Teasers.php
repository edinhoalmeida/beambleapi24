<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *   schema="teasers",
     * @OA\Property(
     *   property="id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="feed_url",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="thumb_url",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="teaser_text",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="categories",
     *  type="array",
     *   @OA\Items(type="integer"),
     * ),
    * @OA\Property(
     *   property="categories_details",
     *  type="array",
     *   @OA\Items(ref="#/components/schemas/categoryshort"),
     * ),
 * ),
 */
class Teasers extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'id' => $this->id ?: 0,
            'feed_url' => $this->feed_url ?: '',
            'thumb_url' => $this->thumb_url ?: '',
            'teaser_text' => $this->teaser_text ?: '',
            'categories' => []
        ];
        return $dados;
    }
}
