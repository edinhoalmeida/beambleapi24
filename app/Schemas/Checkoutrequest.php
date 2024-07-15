<?php
namespace App\Schemas;

/**
 * @OA\Schema(
 *   schema="checkoutrequest",
 *   @OA\Property(
 *       property="products_accepted",
 *       type="array",
 *       @OA\Items(),
 * 		 example={"23","24"}
 *    )
 * )
 */
class Checkoutrequest {}
