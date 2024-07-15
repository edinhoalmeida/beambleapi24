<?php

namespace App\Http\Resources;

use App\Models\User;

use Illuminate\Http\Resources\Json\JsonResource;

// use App\Http\Resources\Image as ImageResource;
// use App\Http\Resources\ImagePh as ImagePhResource;

use App\Http\Resources\ImageB64 as ImageResource;

use App\Models\Category;

/**
 * @OA\Schema(
 *   schema="history",
      * @OA\Property(
     *   property="id",
     *   type="integer",
     * ),
     * @OA\Property(
     *   property="uuid",
     *   type="string",
     * ),
     * @OA\Property(
     *   property="online",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="name",
     *   type="string",
     *   description="Name"
     * ),
     * @OA\Property(
     *   property="surname",
     *   type="string",
     *   description="Surname"
     * ),
     * @OA\Property(
     *   property="image",
     *   type="string",
     *   description="Photo"
     * ),
     * @OA\Property(
     *   property="city_country",
     *   type="string",
     *   description="City, country"
     * ),
     * @OA\Property(
     *   property="gmt_off_set",
     *   type="number" 
     * ),
     * @OA\Property(
     *   property="updated_at",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="updated_group",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="categories",
     *  type="array",
     *   @OA\Items(type="integer"),
     * ),
    * @OA\Property(
     *   property="categories_details",
     *  type="array",
     *   @OA\Items(ref="#/components/schemas/categoryshort"),
     * ),
 * ),
 */
class History extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this_user = User::find($this->user_id);
        $image = route('url_image', $this->user_id);
        
        $store_address = $this_user->some_address();
        if(empty($store_address)){
            $store_address = new \stdClass();
            $store_address->city = null;
            $store_address->country = null;
            $gmt_off_set = 0;
        } else {
            $gmt_off_set = $store_address->raw_off_set;
        }

        $categories = array_map('intval', explode(":", trim($this->categories," :") ));
        $categories_details = Category::get_details($categories);

        $dados = [
            'id' => $this->user_id,
            'uuid' => $this->uuid,
            'online' => $this->online,
            'name' => $this->name,
            'surname' => $this->surname,
            'image' => $image,
            'city_country' => $store_address->city . ", " . $store_address->country,
            'gmt_off_set' => $gmt_off_set,
            'updated_at' => $this->updated_at,
            'updated_group' => $this->updated_group,
            'categories' => $categories,
            'categories_details' => $categories_details,
        ];
        return $dados;
    }
}
