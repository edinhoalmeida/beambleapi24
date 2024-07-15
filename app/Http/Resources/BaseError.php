<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *      schema="baseerror",
 *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          description="Status of request",
 *          example="false",
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="Request failure details",
 *          @OA\Items() 
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Message from api"
 *      ),
* )
 */
class BaseError extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'success' => false,
            'data' => [],
            'message' => "ok"
        ];
        return $dados;
    }
}

