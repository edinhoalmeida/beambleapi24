<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *      schema="baseusers",
  *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          description="Status of request"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="object",
 *          description="Request datas",
   *        @OA\Property(
 *              property="token",
 *              type="string",
 *              description="Used to required auth routes in Baerer header"
 *          ),
    *        @OA\Property(
 *              property="name",
 *              type="string",
 *          ),
 *          @OA\Property(
 *              property="interface_as",
 *              type="string",
 *              description="client or beamer"
 *          ),
    *      @OA\Property(
    *          property="user",
    *          type="object",
    *          ref="#/components/schemas/user",  
    *      ),
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Messages from api"
 *      ),
 * )
 */
class BaseUsers extends ResourceCollection
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

