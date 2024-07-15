<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *      schema="base",
 *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          description="Status of request"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="Requested datas",
 *          @OA\Items() 
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Messages from api"
 *      ),
 * )
 */
class Base extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'success' => true,
            'data' => [],
            'message' => "ok"
        ];
        return $dados;
    }
}

