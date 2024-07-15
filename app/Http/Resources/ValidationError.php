<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *   schema="validationerror",
     * @OA\Property(
     *   property="success",
     *   type="boolean",
     *   description="validation status"
     * ),
     * @OA\Property(
     *   property="message",
     *   type="string",
     *   description="validation message"
     * ),
    *  @OA\Property(
    *    description="Errors",
    *    property="data",
    *    type="object",
    *    additionalProperties={
    *      "type": "array"
    *     },  
     *  ),
 * ),
 */
class ValidationError extends ResourceCollection
{

    public function toArray($request)
    {
        $dados = [
            'success' => false,
            'data' => ['email'=>["Email already registered"]],
            'message' => "Auth error. Header Baerer is required.",
        ];
        return $dados;
    }
}
