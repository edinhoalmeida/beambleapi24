<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\ImageB64 as ImageResource;

/**
 * @OA\Schema(
 *   schema="product",
     * @OA\Property(
     *   property="product_id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="videocall_id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="status",
     *   type="string",
     *   enum={"new", "read", "accepted", "rejected"},
     *   description="Status of this product"
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
     *   description="Base64 encoded product image"
     * ),
  * )
 */
class VideocallProduct extends JsonResource
{

    public function toArray($request)
    {
        $product = $this->product()->first();
        $product_image = route('url_product', $product->id);
        $dados = [
            'product_id' => $product->id,
            'videocall_id' => $this->videocall_id,
            'status' => $this->status,
            'product_price' => $product->product_price,
            'product_currency' => $product->product_currency,
            'title' => $product->title,
            'description' => $product->description,
            'brand_name' => $product->brand_name,
            'color' => $product->color,
            'fabric' => $product->fabric,
            'condition' => $product->condition,
            'size' => $product->size,
            'product_image'=> $product_image
        ];
        return $dados;
    }
}
