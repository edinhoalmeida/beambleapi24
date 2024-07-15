<?php
namespace App\Schemas;

/**
 * @OA\Schema(
 *   schema="category",
 *    @OA\Property(
 *       property="id",
 *       type="integer"
 *    ),
 *    @OA\Property(
 *       property="parent_id",
 *       type="integer",
 *       nullable=True
 *    ),
 *    @OA\Property(
 *       property="title",
 *       type="string"
 *    ),
*     @OA\Property(
 *       property="icon_url",
 *       type="string"
 *    ),
 *    @OA\Property(
 *       property="ionicons_class",
 *       type="string",
 *    ),
 *    @OA\Property(
 *       property="subcategories",
 *       type="array",
 *       nullable=True,
 *       @OA\Items(ref="#/components/schemas/subcategory") 
 *    )
 * )
 */
class Category {}
