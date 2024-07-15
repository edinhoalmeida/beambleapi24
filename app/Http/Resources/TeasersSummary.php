<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *   schema="teaserssummary",
     * @OA\Property(
     *   property="thumb_url",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="category_name",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="category_ionicons_class",
     *   type="string"
     * ),
 * ),
 */
class TeasersSummary extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'feed_url' => '',
            'category_name' => '',
            'category_ionicons_class' => '',
        ];
        return $dados;
    }
}
