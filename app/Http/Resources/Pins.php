<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Category;
use App\Models\Robots\VideosJob;

use Illuminate\Http\Resources\Json\JsonResource;

// use App\Http\Resources\Image as ImageResource;
// use App\Http\Resources\ImagePh as ImagePhResource;

use App\Http\Resources\ImageB64 as ImageResource;

/**
 * @OA\Schema(
 *   schema="pins",
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
     *   property="surname",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="pref_lang",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="beamer_type",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="with_donation",
     *   type="integer"
     * ),
    * @OA\Property(
     *   property="is_freemium",
     *   type="integer"
     * ),
    * @OA\Property(
     *   property="cost_per_minute",
     *   type="number"
     * ),
    * @OA\Property(
     *   property="keywords",
     *      type="array",
     *      @OA\Items() 
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
    * @OA\Property(
     *   property="image",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="logo",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="feed_url",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="thumb_url",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="teaser_text",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="teaser_style",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="lat",
     *   type="number"
     * ),
     * @OA\Property(
     *   property="lng",
     *   type="number"
     * ),
    * @OA\Property(
     *   property="relevance",
     *   type="integer"
     * ),
    * @OA\Property(
     *   property="uuid",
     *   type="string"
     * ),
    * @OA\Property(
     *   property="share_url",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="website",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="rating",
     *   type="number" 
     * ),
     * @OA\Property(
     *   property="gmt_off_set",
     *   type="number" 
     * ),
     * @OA\Property(
     *   property="address",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="postal_code",
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
     *   property="company_name",
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
 * ),
 */
class Pins extends JsonResource
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
        // if($image = $this_user->image()){
        //     $image = new ImageResource($image);
        //     $image = $image->base64;
        // } else {
        //     $image = ImageResource::placeholder();
        // }

        // if($logo = $this_user->logo()){
        //     $logo = new ImageResource($logo);
        //     $logo = $logo->base64;
        // } else {
        //     $logo = ImageResource::placeholder_logo();
        // }
        $image = route('url_image', $this->user_id);
        $logo = route('url_logo', $this->user_id);

        if($this_user->videofeed->isEmpty()){
            $feed_url = $thumb_url = $teaser_text = $teaser_style = null;
        } else {
            $AWS_CDN = env('AWS_CDN');
            $videofeed = $this_user->videofeed->first();
            if(empty($AWS_CDN)){
                $feed_url = route('url_video', $videofeed->id);
                $thumb_url = route('url_thumb', $videofeed->id);
            } else {
                $feed_url = $AWS_CDN. '/'. $videofeed->converted;
                $thumb_url = $AWS_CDN. '/'. $videofeed->thumb;
            }

            if($this->user_id==525){
                $videofeed->original = 'beamble_tests1.mp4';
            }elseif(in_array($this->user_id,[544,547,562])){
                $videofeed->original = 'beamble_tests2.mp4';
            }

            if($tem_no_bytescale = VideosJob::get_urls_bytescale($videofeed->original)){
                // sobrescreverá $feed_url e $thumb_url 
                extract($tem_no_bytescale);
            }

            $teaser_text = $videofeed->teaser_text;
            $teaser_style = $videofeed->teaser_style;
        }

        // $feed_url = route('url_video', $videofeed->id);
        // $thumb_url = route('url_thumb', $videofeed->id);

    

    // $lines = explode("\n",$byteencode);
        
    //     shuffle($lines);
    //     $lii = current($lines);
        // list($feed_url, $thumb_url) = explode(",", $edinho);
        // dd($feed_url);

        $event_title = empty($this->event_title) ? 'téléportation' : $this->event_title;
        $event_title =  ucfirst($event_title);

        $track_keywords = empty($this->track_keywords) ? [] : explode(' ; ',$this->track_keywords);
        $user_keywords = empty($this_user->keywords) ? [] : explode(' ; ',$this_user->keywords);
        $keywords = array_unique( array_merge($track_keywords, $user_keywords) );

        $relevance = 0;
        if(!empty($this->text_matches)){
            $relevance += (int) $this->text_matches;
        }elseif(!empty($this->relevance)){
            $relevance += (int) $this->relevance;
        }

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

        $store_address = $this_user->store_address();
        if(empty($store_address)){
            $store_address = new \stdClass();
            $store_address->address = null;
            $store_address->postal_code = null;
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
            'online' => $this->online,
            'name' => $this->name,
            'surname' => $this->surname,
            'minibio' => $this->minibio,
            'pref_lang' => $this_user->prefLang(),
            'beamer_type'=> $this->beamer_type,
            'with_donation'=> $this->with_donation,
            'is_freemium'=> $this->is_freemium,
            'cost_per_minute'=> (float) $this->cost_per_minute,
            'event_title'=> $event_title,
            'keywords'=> $keywords,
            'categories'=> $categories,
            'categories_details'=> $categories_details,
            'image' => $image,
            'logo' => $logo,
            'feed_url' => $feed_url,
            'thumb_url' => $thumb_url,
            'teaser_text' => $teaser_text,
            'teaser_style' => $teaser_style,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'relevance' => $relevance,
            'uuid' => $this->uuid,
            'share_url' => route('share_user', $this->uuid),
            'website' => $this_user->website,
            'rating' => $this_user->rating(),
            'gmt_off_set' => $gmt_off_set,
            'address' => $store_address->address,
            'postal_code' => $store_address->postal_code,
            'city' => $store_address->city,
            'country' => $store_address->country,
            'company_name' => $this_user->company_name,
            'company_type' => $this_user->company_type,
            'followers' => $followers,
            // 'street' => $this->street,
        ];
        return $dados;
    }
}
