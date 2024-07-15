<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Category;

use Illuminate\Http\Resources\Json\JsonResource;

// use App\Http\Resources\Image as ImageResource;
// use App\Http\Resources\ImagePh as ImagePhResource;

use App\Http\Resources\ImageB64 as ImageResource;

/**
 * @OA\Schema(
 *   schema="pinssummary",
     * @OA\Property(
     *   property="id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="online",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="name",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="cost_per_minute",
     *   type="number"
     * ),
    * @OA\Property(
     *   property="image",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="rating",
     *   type="number" 
     * ),
     * @OA\Property(
     *   property="location",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="company_type",
     *   type="string" 
     * ),
    * @OA\Property(
     *   property="followers",
     *   ref="#/components/schemas/follow"
     * ),
    * @OA\Property(
     *   property="all_teasers_summary",
     *  type="array",
     *   @OA\Items(ref="#/components/schemas/teaserssummary"),
     * ),
 * ),
 */
class PinsSummary extends JsonResource
{

    public static $logged_userid = null;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this_user = User::find($this->user_id);

        $user_video_feed = $this_user->feed_and_teasers(2);
        $all_teasers = $user_video_feed['all_teasers'];
        $all_teasers_summary = [];
        foreach($all_teasers as $teaser){
            if(!empty($teaser['categories_details'][0])) {
                $category_name = $teaser['categories_details'][0]->title;
                $category_ionicons_class = $teaser['categories_details'][0]->ionicons_class;
            } else {
                $category_name = null;
                $category_ionicons_class = null;
            }
            $all_teasers_summary[] = [
                'teaser_id' => $teaser['thumb_url'],
                'thumb_url' => $teaser['thumb_url'],
                'category_name' => $category_name,
                'category_ionicons_class' => $category_ionicons_class,
            ];
        }

        $image = route('url_image', $this->user_id);

        $followers = new \stdClass;
        $followers->total = count($this_user->followers);
        $followers->this_user_follow = 0;
        if(!empty(Pins::$logged_userid)){
            foreach($this_user->followers as $foll){
               if($foll->follower_id==Pins::$logged_userid){
                    $followers->this_user_follow = 1;
                    break;
               } 
            }
        } else {
            $followers->this_user_follow = 0;
        }

        $store_address = $this_user->some_address();
        if(empty($store_address)){
            $store_address = new \stdClass();
            $store_address->city = null;
            $store_address->country = null;
        } 

        $dados = [
            'id' => $this->user_id,
            'online' => $this->online,
            'name' => $this->name,
            'cost_per_minute'=> (float) $this->cost_per_minute,
            'image' => $image,
            'rating' => $this_user->rating(),
            'location' => $store_address->city . ', ' . $store_address->country,
            'company_type' => $this_user->company_type,
            'followers' => $followers,
            'all_teasers_summary' => $all_teasers_summary,
        ];
        return $dados;
    }
}
