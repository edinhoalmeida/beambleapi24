<?php
namespace App\Schemas;

/**
 * @OA\Schema(
 *   schema="subcategory",
 *   @OA\Property(
 *       property="id",
 *       type="integer"
 *   ),
 *   @OA\Property(
 *       property="parent_id",
 *       type="integer",
 *       nullable=True
 *   ),
 *   @OA\Property(
 *       property="title",
 *       type="string"
 *   )
 * )
 */
class Subcategory {}  
