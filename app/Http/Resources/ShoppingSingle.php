<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;


/**
 * @OA\Schema(
 *      schema="shoppingsingle",
 *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          description="Status of request"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="object",
 *          description="Request datas",
 *          @OA\Property(
 *              property="product",
 *              type="object",
 *              ref="#/components/schemas/userproduct"
 *          ),  
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Messages from api"
 *      ),
 * )
 */
class ShoppingSingle extends ResourceCollection
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

