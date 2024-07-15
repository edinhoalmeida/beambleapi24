<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

// use App\Http\Resources\Image as ImageResource;
// use App\Http\Resources\ImagePh as ImagePhResource;
// use App\Http\Resources\ImageB64 as ImageResource;

use App\Models\UserPolyData;

/**
 * @OA\Schema(
 *   schema="user",
     * @OA\Property(
     *   property="id",
     *   type="integer"
     * ),
     * @OA\Property(
     *   property="email_verified",
     *   type="boolean"
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
     *   property="email",
     *   type="string"
     * ),
      * @OA\Property(
     *   property="position",
     *   type="string",
     *   description="on_line or off_line"
     * ),
     * @OA\Property(
     *   property="interface_as",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="keywords",
    *       nullable=true, 
     *   type="array",
     *   @OA\Items()
     * ),
       * @OA\Property(
     *   property="pref_lang",
     *   type="string" 
     * ),
       * @OA\Property(
     *   property="pref_lang_name",
     *   type="string" 
     * ),
    * @OA\Property(
     *   property="all_lang_names",
     *   type="array",
     *   @OA\Items()
     * ),
    * @OA\Property(
     *   property="client_enabled",
     *   type="boolean"
     * ),
       * @OA\Property(
     *   property="beamer_enabled",
     *   type="boolean"
     * ),
     * @OA\Property(
     *   property="shopper_enabled",
     *   type="boolean"
     * ),
     * @OA\Property(
     *   property="quickon_enabled",
     *   type="boolean",
     *   description="Beamer is able to do a quick online" 
     * ),
     * @OA\Property(
     *   property="client_account",
     *   type="boolean"
     * ),
     * @OA\Property(
     *   property="beamer_account",
     *   type="boolean"
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
     *   property="uuid",
     *   type="string" 
     * ), 
     * @OA\Property(
     *   property="share_url",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="rating",
     *   type="number" 
     * ),
     * @OA\Property(
     *   property="deals",
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
     *   property="website",
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
     *   property="company_doc",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="second_hand_resaler",
     *   type="number" 
     * ),
     * @OA\Property(
     *   property="level_expertise",
     *   type="string" 
     * ),
     * @OA\Property(
     *   property="accept_parcel_return",
     *   type="number" 
     * ),
    * @OA\Property(
     *   property="followers",
     *   ref="#/components/schemas/follow"
     * ),
  * ),
 */
class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // if($image = $this->image()){
        //   $image = new ImageResource($image);
        //   $image = $image->base64;
        // } else {
        //   $image = ImageResource::placeholder();
        // }

        // if($logo = $this->logo()){
        //   $logo = new ImageResource($logo);
        //   $logo = $logo->base64;
        // } else {
        //   $logo = ImageResource::placeholder_logo();
        // }

        $image = route('url_image', $this->id);
        $logo = route('url_logo', $this->id);

        $keywords = strpos($this->keywords, ' ; ')!==false 
            ? explode(' ; ', $this->keywords)
            : [ $this->keywords ];


        if($this->interface_as=='beamer'){
            $store_address = $this->beamer_address();
        } else {
            $store_address = $this->client_address();
        }
        
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

        $followers = new \stdClass;
        $followers->total = count($this->followers);
        $filtered = $this->followers->filter(function ($value, $key) {
          return $value->follower_id == $value->user_id;
        });
        $followers->this_user_follow = $filtered->count();

        $prefLang = $language_name = $this->prefLang();
        $languages_list = config('language.languages_to_json');
        $languages_list = collect($languages_list);
        $filtered = $languages_list->firstWhere('code', $prefLang);

        if(!empty($filtered->name)){
            $language_name = $filtered->name;
        }

        $all_lang_names = [];
        $all_lang = [];
        foreach($this->lang as $ll) {
            $all_lang[] = $ll->lang_code;
            $filtered = $languages_list->firstWhere('code', $ll->lang_code);
            if(!empty($filtered->name)){
                $all_lang_names[] = $filtered->name;
            } else {
                $all_lang_names[] = $ll->lang_code;
            }
        }

        $flags = [
            'pref_lang' => $prefLang,
            'pref_lang_name' => $language_name,
            'all_lang' => $all_lang,
            'all_lang_names' => $all_lang_names,
            'client_enabled' => $this->client_enabled,
            'beamer_enabled' => $this->beamer_enabled,
            'shopper_enabled' => $this->shopper_enabled,
            'quickon_enabled' => $this->quick_on,
            'address_enabled' => $this->address_enabled,
            'beamer_account' => $this->beamer_account,
            'client_account' => $this->client_account,
            'rating' => $this->rating(),
            'deals' => $this->deals()
        ];

        $dados = [
            'id' => $this->id,
            'email_verified' => empty($this->email_verified_at) ? false : true,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'interface_as' => $this->interface_as,
            'position' => $this->position,
            'phone' => $this->phone,
            'keywords' => $keywords,
            'image' => $image,
            'logo' => $logo,
            'uuid' => $this->uuid,
            'share_url' => route('share_user', $this->uuid),
            'gmt_off_set' => $gmt_off_set,
            'address' => $store_address->address,
            'postal_code' => $store_address->postal_code,
            'city' => $store_address->city,
            'country' => $store_address->country,
            'website' => $this->website,
            'company_name' => $this->company_name,
            'company_type' => $this->company_type,
            'company_doc' => $this->company_doc,
            'followers' => $followers,
            'second_hand_resaler' => 0,
            'level_expertise' => null,
            'accept_parcel_return' => 0
        ];


        // dd($dados);
        if($this->interface_as=='beamer'){
            $beamer_data = UserPolyData::get_beamer_data($this->id);
            $dados = array_merge($dados, $beamer_data);
        } else if($this->interface_as=='client'){
            $client_data = UserPolyData::get_client_data($this->id);
            $dados = array_merge($dados, $client_data);
        } else {
            // by pass
        }


        // dd($obj_merged);
        return $dados + $flags;
    }
}
