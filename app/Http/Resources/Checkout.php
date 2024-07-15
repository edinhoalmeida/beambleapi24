<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *      schema="checkout",
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
    *          @OA\Property(
    *              property="shipping",
    *              type="object",
    *              @OA\Property(
    *                  property="shipping_cost",
    *                  type="string",
    *              ),
    *              @OA\Property(
    *                  property="shipping_service",
    *                  type="string",
    *              ),
    *          ), 
    *          @OA\Property(
    *              property="address",
    *              type="object",
    *              @OA\Property(
    *                  property="address",
    *                  type="string",
    *              ),
    *              @OA\Property(
    *                  property="postal_code",
    *                  type="string",
    *              ),
    *              @OA\Property(
    *                  property="city",
    *                  type="string",
    *              ),
    *              @OA\Property(
    *                  property="country",
    *                  type="string",
    *              ),
    *          ), 
    *          @OA\Property(
    *              property="checkout",
    *              type="object",
    *              @OA\Property(
    *                  property="call_cost",
    *                  type="integer",
    *              ),
    *              @OA\Property(
    *                  property="products_cost",
    *                  type="integer",
    *              ),
    *              @OA\Property(
    *                  property="shipping_cost",
    *                  type="integer",
    *              ),
    *              @OA\Property(
    *                  property="use_wallet_amount",
    *                  type="integer",
    *              ),
    *              @OA\Property(
    *                  property="total_cost",
    *                  type="integer",
    *              ),
    *              @OA\Property(
    *                  property="currency",
    *                  type="string",
    *              ),
    *          ), 
    *          @OA\Property(
    *              property="wallet",
    *              type="object",
    *              @OA\Property(
    *                   property="wallet_balance",
    *                   type="integer",
    *              ),
    *          ),  
 *         ),  
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Messages from api"
 *      ),
 * )
 */
class Checkout extends ResourceCollection
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

