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
 *   schema="profile",
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
     *   property="company_type",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="company_name",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="city",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="country",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="location",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="languages_code",
     *   type="array",
     *   @OA\Items()
     * ),
     * @OA\Property(
     *   property="languages_names",
     *   type="array",
     *   @OA\Items()
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
     *   property="rating_details",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="deals",
     *   type="integer"
     * ),
     * 
    * @OA\Property(
     *   property="uuid",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="share_url",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="gmt_off_set",
     *   type="number" 
     * ),
    * @OA\Property(
     *   property="all_teasers",
     *  type="array",
     *   @OA\Items(ref="#/components/schemas/teasers"),
     * ),
    * @OA\Property(
     *   property="followers",
     *   ref="#/components/schemas/follow"
     * ),
    * @OA\Property(
     *   property="followers_details",
     *   type="array",
     *   @OA\Items(ref="#/components/schemas/followers"),
     * ),
 * ),
 */
class Profile extends JsonResource
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
        
        $user_video_feed = $this_user->feed_and_teasers();

        $feed_url =   $user_video_feed['feed_url'];
        $thumb_url =   $user_video_feed['thumb_url'];
        $teaser_text =  $user_video_feed['teaser_text'];
        $teaser_style =  $user_video_feed['teaser_style'];
        $teaser_categories = $user_video_feed['teaser_categories'];
        $teaser_categories_details = $user_video_feed['teaser_categories_details'];
        $all_teasers =   $user_video_feed['all_teasers'];

        $image = route('url_image', $this->user_id);
        $logo = route('url_logo', $this->user_id);

        $event_title = empty($this->event_title) ? 'téléportation' : $this->event_title;
        $event_title =  ucfirst($event_title);

        $followers = new \stdClass;
        $followers->total = count($this_user->followers);
        $followers->this_user_follow = 0;

        $followers_details = [];
        foreach($this_user->followers as $foll){
            $f_details = new \stdClass;
            $f_details->name = $foll->follower->name;
            $f_details->image = route('url_image', $foll->follower->id);
            $foll_address = $foll->follower->some_address();
            $f_details->city_country =  $foll_address->city . ', ' . $foll_address->country;
            $followers_details[] = $f_details;
            if(!empty(Profile::$logged_userid) && $foll->follower->id==Profile::$logged_userid){
                $followers->this_user_follow = 1;
            }
        }

        $store_address = $this_user->some_address();
        if(empty($store_address)){
            $store_address = new \stdClass();
            $store_address->address = null;
            $store_address->postal_code = null;
            $store_address->city = null;
            $store_address->country = null;
            $gmt_off_set = 0;
            $location ='';
        } else {
            $gmt_off_set = $store_address->raw_off_set;
            $location =  $store_address->city . ", " . $store_address->country;
        }

        $keywords = [];
        $categories = array_map('intval', explode(":", trim($this->categories," :") ));

        $actual_langs =  $this_user->lang;
        $langs = [];
        foreach($actual_langs as $lang){
            $langs[] = $lang->lang_code;
        }
        $all_langs = collect( config('language.languages_to_json') );
        $all_langs_filtered = $all_langs->filter(function ($value, $key) use ($langs) {
            return in_array($value->code, $langs);
        });
        $langs_names = [];
        foreach($all_langs_filtered as $lang){
            $langs_names[] = $lang->name;
        }

        $dados = [
            'id' => $this->user_id,
            'online' => $this->online,
            'name' => $this->name,
            'company_type' => $this_user->company_type,
            'company_name' => $this_user->company_name,
            'city' => $store_address->city,
            'country' => $store_address->country,
            'location'=>$location,
            'languages_code' => $langs,
            'languages_names' => $langs_names,
            'image' => $image,
            'rating' => $this_user->rating(),
            'rating_details' => $this_user->rating_total(),
            'deals' => $this_user->deals(),
            'uuid' => $this->uuid,
            'share_url' => route('share_user', $this->uuid),
            'gmt_off_set' => $gmt_off_set,
            'all_teasers' =>$all_teasers,
            'followers' => $followers,
            'followers_details' => $followers_details,
        ];
        return $dados;
    }
}
