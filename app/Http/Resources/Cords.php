<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *   schema="cords",
 * ),
 * @OA\Property(
 *   property="lat0",
 *   type="number",
 *   description="Top right Latitute"
 * ),
 * @OA\Property(
 *   property="lng0",
 *   type="number",
 *   description="Top right Longitude"
 * ),
 * @OA\Property(
 *   property="lat1",
 *   type="number",
 *   description="Bottom left Latitute"
 * ),
 * @OA\Property(
 *   property="lng1",
 *   type="number",
 *   description="Bottom left Longitude"
 * ),
 */
class Cords extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'lat0' => 20,
            'lng0' => -20,
            'lat1' => -20,
            'lng1' => 20
        ];
        return $dados;
    }
}
