<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *   schema="autherror",
 * ),
 * @OA\Property(
 *   property="success",
 *   type="boolean",
 *   description="request status"
 * ),
 * @OA\Property(
 *   property="message",
 *   type="boolean",
 *   description="request message"
 * )
 */
class AuthError extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'success' => false,
            'message' => "Auth error. Header Baerer is required.",
        ];
        return $dados;
    }
}
