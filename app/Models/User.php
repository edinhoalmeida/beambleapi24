<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

use App\Models\ImageB64;
use App\Models\Category;

use App\Models\UserStripeCustomer;
use App\Models\UserStripeAccount;
use App\Models\UserShopper;
use App\Models\UserTrack;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use SoftDeletes;
    use MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname','email', 'password','modifier_id', 'ui', 'my_language',
        'phone', 'position', 'birthday', 'interface_as', 'minibio', 'keywords'
        , 'tos_accepted_at','is_generic', 'uuid','company_name','company_doc','company_type','company_description','website','accept_parcel_return'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function get_permissions()
    {
        $roles = $this->roles;
        if(!empty($roles[0])) {
            $perms = $roles[0]->permissions->pluck('name');
            return $perms->toArray();
        }
        return [];
    }

    public function getFullNameAttribute()
    {
        return "{$this->surname}, {$this->name}";
    }

    public function eraseAccount()
    {
        UserTrack::force_end_track($this->id);
        $this->address()->delete();
        $this->allimages()->delete();
        $this->email = 'eraseAccount'. $this->id . '@eraseAccount.com';
        $this->name = '-- erasedAccount -- ';
        $this->surname = '-- erasedAccount -- ';
        $this->save();
        $this->delete();
    }

    public function setUuid()
    {
        if(empty($this->uuid)) {
            $this->uuid = (string) Str::uuid()->toString();
            $this->save();
        }
    }

    public function address()
    {
        return $this->hasMany(UserAddresses::class);
    }

    public function some_address()
    {
        $sorted = $this->address->sortBy(function ($value, $key) {
            if($value->address_type == "store"){
                return 0;
            }
            if($value->address_type == "shipping"){
                return 1;
            }
            if($value->address_type == "billing"){
                return 2;
            }
            if($value->address_type == "contact"){
                return 3;
            }
            return 4;
        });
        if($sorted->count()==0){
            return [];
        }
        return $sorted->first();
    }

    public function beamer_address()
    {
        $sorted = $this->address->sortBy(function ($value, $key) {
            if($value->address_type == "store"){
                return 0;
            }
            if($value->address_type == "shipping"){
                return 1;
            }
            if($value->address_type == "billing"){
                return 2;
            }
            if($value->address_type == "contact"){
                return 3;
            }
            return 4;
        });
        if($sorted->count()==0){
            return [];
        }
        return $sorted->first();
    }

    public function client_address()
    {
        $sorted = $this->address->sortBy(function ($value, $key) {
            if($value->address_type == "contact"){
                return 0;
            }
            if($value->address_type == "shipping"){
                return 1;
            }
            if($value->address_type == "billing"){
                return 2;
            }
            if($value->address_type == "store"){
                return 3;
            }
            return 4;
        });
        if($sorted->count()==0){
            return [];
        }
        return $sorted->first();
    }

    public function store_address()
    {
        $filtered = $this->address->filter(function ($value, $key) {
            return $value->address_type == "store";
        });
        if($filtered->count()==0){
            return $this->contact_address();
        }
        return $filtered->first();
    }

    public function billing_address()
    {
        $filtered = $this->address->filter(function ($value, $key) {
            return $value->address_type == "billing";
        });
        if($filtered->count()==0){
            return $this->contact_address();
        }
        return $filtered->first();
    }

    public function contact_address()
    {
        $filtered = $this->address->filter(function ($value, $key) {
            return $value->address_type == "contact";
        });
        return $filtered->first();
    }

    public function shipping_address()
    {
        $filtered = $this->address->filter(function ($value, $key) {
            return $value->address_type == "shipping";
        });
        if($filtered->count()==0){
            return $this->contact_address();
        }
        return $filtered->first();
    }

    public function followers()
    {
        return $this->hasMany(UserFollowers::class);
    }

    public function lang()
    {
        return $this->hasMany(UserLang::class)->orderBy('id', 'asc');
    }

    public function save_langs($langs)
    {
        $actual_langs =  $this->lang;
        foreach($actual_langs as $rel) {
            $rel->delete();
        }
        if(!empty($langs)) {
            $this->lang()->createMany($langs);
        }
    }

    public function prefLang()
    {
        $pref =  $this->lang()->first();
        if(empty($pref)) {
            return null;
        }
        return $pref['lang_code'];
    }

    public function image()
    {
        $filtered = $this->allimages->filter(function ($value, $key) {
            return $value->type == "profile";
        });
        return $filtered->first();
    }

    public function logo()
    {
        $filtered = $this->allimages->filter(function ($value, $key) {
            return $value->type == "logo";
        });
        return $filtered->first();
    }

    public function allimages()
    {
        return $this->morphMany(ImageB64::class, 'imageable');
    }

    public function videofeed()
    {
        return $this->hasMany(UserVideofeed::class)->orderBy('id', 'desc');
    }

    public function customer()
    {
        return $this->hasOne(UserStripeCustomer::class);
    }

    public function getClientEnabledAttribute()
    {
        $customer = $this->customer;
        if(empty($customer->customer_id) || empty($customer->customer_stripe_enabled)) {
            if(true || is_defined('STRIPE_CLIENT_AUTO_ENABLED')) {
                $this->customer()->updateOrCreate(
                    ['user_id' => $this->id],
                    [
                        'customer_id' => 'cus_PlxwVtHPDfSCsc',
                        'customer_stripe_enabled' => 1,
                    ]
                );
                return true;
            }
            return false;
        }
        return true;
    }

    public function beamer()
    {
        return $this->hasOne(UserStripeAccount::class);
    }
    public function getBeamerEnabledAttribute()
    {
        $beamer = $this->beamer;
        if(empty($beamer->account_id) || empty($beamer->account_stripe_enabled)) {
            if(true || is_defined('STRIPE_BEAMER_AUTO_ENABLED')) {
                $this->beamer()->updateOrCreate(
                    ['user_id' => $this->id],
                    [
                        'account_id' => 'acct_1NsZrO2fctvy0N4m',
                        'account_stripe_enabled' => 1,
                    ]
                );
                return true;
            }
            return false;
        }
        return true;
    }

    public function shopper()
    {
        return $this->hasOne(UserShopper::class);
    }

    public function getShopperEnabledAttribute()
    {
        $shopper = $this->shopper;
        if(empty($shopper->shopper_enabled)) {
            if(true || is_defined('STRIPE_SHOPPER_AUTO_ENABLED')) {
                $this->shopper()->updateOrCreate(
                    ['user_id' => $this->id],
                    [
                        'user_id' => $this->id,
                        'shopper_enabled' => 1,
                    ]
                );
                return true;
            }
            return false;
        }
        return true;
    }

    public function getQuickOnAttribute()
    {
        $has_track = UserTrack::where('user_id', $this->id)->orderBy('updated_at', 'desc')->withTrashed()->first();
        if(empty($has_track)) {
            return false;
        }
        return true;
    }

    public function getAddressEnabledAttribute()
    {
        return $this->address->count() > 0;
    }

    public function rating()
    {
        $sql = "
        select AVG(rating) as rating from videocalls_rating where side='client'
        AND call_id in
        (
            select id from videocalls where beamer_id = ? and status = ?            
        )
        ";
        $rating = \DB::select($sql, [$this->id, 'accepted']);
        if(empty($rating[0]->rating)) {
            return 3.0;
        }
        return (float) $rating[0]->rating;
    }
        public function rating_total()
        {
            $sql = "
            select count(rating) as rating_total from videocalls_rating where side='client'
            AND call_id in
            (
                select id from videocalls where beamer_id = ? and status = ?            
            )
            ";
            $rating = \DB::select($sql, [$this->id, 'accepted']);
            if(empty($rating[0]->rating_total)) {
                $total = random_int(37, 158);
            } else {
                $total = (int) $rating[0]->rating_total;
            }
            $total = floor($total/10) * 10;
            return '+' . $total;
        }

    public function deals()
    {
        return random_int(2, 123);
    }

    public function feed_and_teasers($limit = null){
        
        $user_video_feed = $this->videofeed;
        
        if($user_video_feed->count()==0){
            $feed_url = $thumb_url = $teaser_text = $teaser_style = null;
            $categories1 = null;
            $categories1_details = null;
            $all_teasers = [];
        } else {
            if($limit){
                $all_teasers_ar = $user_video_feed->all();
                if(count($all_teasers_ar)>$limit){
                    $all_teasers_ar = array_slice($all_teasers_ar, 0, $limit);
                }
            } else {
                $all_teasers_ar = $user_video_feed->all();
            }
            $videofeed = $user_video_feed->first();
            $AWS_CDN = env('AWS_CDN');
            if(empty($AWS_CDN)){
                $feed_url = route('url_video', $videofeed->id);
                $thumb_url = route('url_thumb', $videofeed->id);
            } else {
                $feed_url = $AWS_CDN. '/'. $videofeed->converted;
                $thumb_url = $AWS_CDN. '/'. $videofeed->thumb;
            }
            $categories1 = array_map('intval', explode(":", trim($videofeed->categories()," :") ));
            $categories1_details = Category::get_details($categories1);

            $teaser_text = $videofeed->teaser_text;
            $teaser_style = $videofeed->teaser_style;

            foreach($all_teasers_ar as $teaser){
                if(empty($AWS_CDN)){
                    $feed_url2 = route('url_video', $teaser->id);
                    $thumb_url2 = route('url_thumb', $teaser->id);
                } else {
                    $feed_url2 = $AWS_CDN. '/'. $teaser->converted;
                    $thumb_url2 = $AWS_CDN. '/'. $teaser->thumb;
                }
                $categories2 = array_map('intval', explode(":", trim($teaser->categories()," :") ));
                $all_teasers[] = [
                    'id' => $teaser->id,
                    'feed_url' => $feed_url2,
                    'thumb_url' => $thumb_url2,
                    'teaser_text' => $teaser->teaser_text,
                    'categories' => $categories2,
                    'categories_details' => Category::get_details($categories2)
                ];
            }
        }

        return [
            'feed_url' =>   $feed_url,
            'thumb_url' =>   $thumb_url,
            'teaser_text' =>  $teaser_text,
            'teaser_style' =>  $teaser_style,
            'teaser_categories' => $categories1,
            'teaser_categories_details' => $categories1_details,
            'all_teasers' =>   $all_teasers,
        ];
    }

    public function devices()
    {
        return $this->hasMany(UserDevices::class)->orderBy('id', 'desc');
    }

    public function products()
    {
        return $this->hasMany(UserProducts::class)->orderBy('id', 'desc');
    }

    public function getClientAccountAttribute()
    {
        return true;
    }

    public function getBeamerAccountAttribute()
    {
        $has = UserBeamerData::where('user_id', $this->id)->first();
        if(!empty($has->company_type) AND !empty($has->company_doc)){
            return true;    
        }
        return false;
    }
}
