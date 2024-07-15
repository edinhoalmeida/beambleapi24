<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *      schema="shoppinglist",
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
 *              property="products",
 *              type="array",
 *              @OA\Items(ref="#/components/schemas/product") 
 *          ),  
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Messages from api"
 *      ),
 * )
 */
class BaseShopping extends ResourceCollection
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

