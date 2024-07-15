<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="userproduct",
     * @OA\Property(
     *   property="id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="product_price",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="product_currency",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="title",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="description",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="brand_name",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="color",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="fabric",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="condition",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="size",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="product_image",
     *   type="string",
     *   description="url of image"
     * ),
  * )
 */
class UserProduct extends JsonResource
{
    public function toArray($request)
    {
        $product_image = route('url_product', $this->id);
        $dados = [
            'product_id' => $this->id,
            'product_price' => $this->product_price,
            'product_currency' => $this->product_currency,
            'title' => $this->title,
            'description' => $this->description,
            'brand_name' => $this->brand_name,
            'color' => $this->color,
            'fabric' => $this->fabric,
            'condition' => $this->condition,
            'size' => $this->size,
            'product_size'=>$this->product_size,
            'product_weight'=>$this->product_weight,
            'product_image'=> $product_image,
            'created_at'=> $this->created_at->format('d.m.Y'),
        ];
        return $dados;
    }
}
