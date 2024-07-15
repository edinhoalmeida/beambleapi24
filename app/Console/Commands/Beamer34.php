<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\UserTracking;
use App\Models\UserTrack;
use App\Models\UserPolyData;
use App\Models\Videocall;
use App\Models\ImageB64;
use App\Models\Category;
use App\Models\UserVideofeed;
use App\Models\UserFollowers;

use App\Libs\WorldAddress;

use Carbon\Carbon;

use DB;

class Beamer34 extends Command
{
    protected $signature = 'beamer34';

    protected $categories = [];

    protected $description = 'Activate 30 random name beamer to track';

    public function __construct()
    {
        parent::__construct();
        $this->categories = (array) Category::get_all();
        foreach ($this->categories as $id => $cat) {
            $this->categories[ $id ] = (array) $cat;
        }
    }


    public function get_categories()
    {
        $ncat = random_int(1, 3);
        shuffle($this->categories);
        $rand_keys = array_slice($this->categories, 0, $ncat);
        $categories = Arr::pluck($rand_keys, 'id');
        return $categories;
    }

    public function generate_random_follows()
    {
        $robos_ids = range(80,109);
        $total = count($robos_ids);
        // delete following between robots
        DB::delete("DELETE FROM user_followers where (user_id BETWEEN 80 AND 109) AND (follower_id BETWEEN 80 AND 109)");
        foreach($robos_ids as $user_id ){
            // each user will follow a random followed user between 3 and total-3
            $createMultipleFollowers = [];
            $rand_keys = array_rand($robos_ids, random_int(3, ($total-3)) );
            foreach($rand_keys as $keyu){
                if($robos_ids[ $keyu ]==$user_id ){
                    continue;
                }
                $createMultipleFollowers[] = ['user_id'=>$user_id,'follower_id'=>$robos_ids[ $keyu ]];
            }
            if(!empty($createMultipleFollowers)){
                UserFollowers::insert($createMultipleFollowers);
            }
        }
    }

    public function handle()
    {
        $capitais = [
            ["cidade" => "Lisboa", "pais" => "Portugal", "latitude" => 38.7252993, "longitude" => -9.1500364, "idioma" => "pt"],
            ["cidade" => "Madrid", "pais" => "Espanha", "latitude" => 40.4167047, "longitude" => -3.7035825, "idioma" => "es"],
            ["cidade" => "Paris", "pais" => "França", "latitude" => 48.8566969, "longitude" => 2.3514616, "idioma" => "fr"],
            ["cidade" => "Berlim", "pais" => "Alemanha", "latitude" => 52.5170365, "longitude" => 13.3888599, "idioma" => "de"],
            ["cidade" => "Roma", "pais" => "Itália", "latitude" => 41.8933203, "longitude" => 12.4829321, "idioma" => "it"],
            ["cidade" => "Atenas", "pais" => "Grécia", "latitude" => 37.9841493, "longitude" => 23.7279843, "idioma" => "el"],
            ["cidade" => "Viena", "pais" => "Áustria", "latitude" => 48.2083537, "longitude" => 16.3725042, "idioma" => "de"],
            ["cidade" => "Budapeste", "pais" => "Hungria", "latitude" => 47.4813897, "longitude" => 19.1460722, "idioma" => "hu"],
            ["cidade" => "Varsóvia", "pais" => "Polônia", "latitude" => 52.2319581, "longitude" => 21.0067249, "idioma" => "pl"],
            ["cidade" => "Praga", "pais" => "República Tcheca", "latitude" => 50.0874654, "longitude" => 14.4212535, "idioma" => "cs"],
        ];

        $eventos = [
            "Spring Lunar Festival",
            "Global Innovation Marathon",
            "Leaders of Tomorrow Summit",
            "International Gastronomy Fair",
            "Festival of Colors",
            "World Sustainability Conference",
            "Star Observation Night",
            "Green Tech Expo",
            "Artificial Intelligence Symposium",
            "Wellness and Yoga Retreat",
            "E-Sports Championship",
            "Desert Music Festival",
            "Digital Entrepreneurship Congress",
            "Eco-Conscious Fashion Week",
            "International Chess Tournament",
            "Futuristic Education Forum",
            "Contemporary Art Exhibition",
            "Annual Charity Gala",
            "Independent Film Festival",
            "Literary Reading Marathon",
            "Urban Art Circuit",
            "Open Innovation Contest",
            "Street Food Festival",
            "Mental Health Seminar",
            "Extreme Adventure Camp",
            "Vintage Vehicle Meetup",
            "Outdoor Jazz Festival",
            "Landscape Photography Workshop",
            "Global Public Policy Forum",
            "Educational Robotics Tournament",
        ];
        shuffle($eventos);
        while(count($eventos) < 50){
            $eventos = array_merge($eventos, $eventos);
        }

        $promocoes = [
            "Last Days Sale",
            "Cash Payment Discount",
            "One-of-a-Kind Item",
            "Weekend Flash Sale",
            "Buy One Get One Free",
            "Clearance Event",
            "Limited Time Offer",
            "Exclusive Online Deals",
            "Seasonal Savings",
            "Holiday Specials",
            "Early Bird Discounts",
            "Midnight Madness Sale",
            "End of Season Clearance",
            "Free Shipping Over $50",
            "New Customer Bonus",
            "Member Exclusive Offers",
            "Bundle and Save",
            "Mystery Discount Day",
            "Loyalty Rewards Deal",
            "Flash Sale Today Only",
            "Warehouse Clearance",
            "Final Reductions",
            "Back to School Sale",
            "Black Friday Deals",
            "Cyber Monday Special",
            "Happy Hour Discounts",
            "2 for 1 Specials",
            "Summer Sale Extravaganza",
            "Winter Warmup Deals",
            "Spring Savings Fling",
        ];
        shuffle($promocoes);
        while(count($promocoes) < 50){
            $promocoes = array_merge($promocoes, $promocoes);
        }

        // delete old images and logos
        // ImageB64::whereIn('imageable_id',range(80,109))->where('imageable_type', 'App\Models\User')->delete();

        $dir1 = resource_path('fakers/imgs/*.jpg');

        // make at least 60 images
        $images_base64 = [];
        foreach (glob($dir1) as $imagefile) {
            $images_base64[] = 'data:image/jpg;base64,' . base64_encode(file_get_contents($imagefile));
        }
        shuffle($images_base64);
        while(count($images_base64)< 50){
            $images_base64 = array_merge($images_base64, $images_base64);
        }

        // dd($images_base64);
        // $videos = UserVideofeed::whereNotNull('thumb')
        //         ->whereNotNull('original')
        //         ->inRandomOrder()
        //         ->limit(140)
        //         ->get()->toArray();
        $user_13 = range(64718,64742);
        $videos = UserVideofeed::whereNotNull('original')
                ->inRandomOrder()
                ->whereIn('id', array_merge([59077, 62776, 62777],$user_13))
                ->get()->toArray();

        while(count($videos) < 60){
            $videos = array_merge($videos, $videos);
        }

        //
        $this->generate_random_follows();

        // generate a fake calls between the robots
        // if this user dont have calls yet generate between 5 and 10 calls
        $clients_testing = [493,499,155,497,498,40,495,494,20,63,19,109,492];
        foreach($clients_testing as $client_id){
            $this_user_video_calls = Videocall::where('client_id', $client_id)->where('status', 'success_with_duration')->where('distance_between_km','!=', '-1')->get();
            if(count($this_user_video_calls)>=5){
                // already has call so update the dates.
            } else {
                // dont have so create a fak calls
                $this_client_call_nr = random_int(5,10);
                $generate = $this_client_call_nr - count($this_user_video_calls);
                $examples = Videocall::where('client_id','!=', $client_id)->where('status','success_with_duration')->where('distance_between_km','!=', '-1')->inRandomOrder()->limit($generate)->get();
                foreach($examples as $example){
                    $new_line = $example->toArray();
                    unset($new_line['id']);
                    $new_line['client_id'] = $client_id;
                    $new_line['beamer_id'] = random_int(80,109);


                    $linn = Videocall::create($new_line);

                    $mutable1 = Carbon::now();
                    $mutable1->subDays(random_int(1,20));

                    $generated_date = $mutable1->format('Y-m-d H:i:s');
                    \DB::update('update videocalls set updated_at = ?, created_at = ? where id = ?',
                        [$generated_date, $generated_date, $linn->id]
                    );
                }
            }
        }

        // Users that already exists id from 80 to 109 will be randomly updated
        $users = User::where('id', '>=', 80)
                ->where('id', '<=', 109)
                ->withTrashed()
                ->get();

        $total = $users->count();
        $online = array_fill(0, floor(0.7*$total), 'online');
        $offline = array_fill(0, ($total-count($online)), 'offline');
        $off_on = array_merge($online, $offline);
        shuffle($off_on);


        foreach ($users as $user){
            //  Undelete
            if($user->trashed()){
                $user->restore();
            }

            // fix olders videofeed without tracking
            $all_teasers_ar = $user->videofeed->all();

            $teasers_id = [];
            $mantem = 3;
            foreach($all_teasers_ar as $teaser){
                if($mantem<0){
                    $teaser->forceDelete();
                    continue;
                }
                $teasers_id[] = $teaser->id;
                $mantem--;
            }
            if(count($teasers_id)){
                $this_users_tracks = UserTrack::where('user_id', $user->id)->whereNull('videofeed_id')->withTrashed()->get();
                foreach($this_users_tracks as $track){
                    $track->videofeed_id = $teasers_id[array_rand($teasers_id)];
                    $track->save();
                }
            }
            // generate a fake calls between the robots
            // if this user dont have calls yet generate between 5 and 10 calls


            // SELECT * FROM `videocalls` WHERE `status` = 'success_with_duration' AND `distance_between_km` != '-1' ORDER BY `duration` DESC LIMIT 50

            $type = random_int(0, 100) > 80 ? 'instore' : 'freelance' ;

            $user->setUuid();
            $user->name = "test: " . fake()->name();
            $user->surname = fake()->lastName();
            $user->is_generic = 0;
            $user->interface_as = 'beamer';
            $user->company_name = fake()->company();
            $user->company_type = $type;
            $user->position = 'on_line';
            // set new values
            $user->save();
            UserPolyData::set_beamer_data($user->id);
            UserPolyData::set_client_data($user->id);


            $profile_image = array_pop($images_base64);
            // grava imagem
            $imageable = new ImageB64([
                'base64' => $profile_image,
                'modifier_id' => $user->id,
                'type' => 'profile',
            ]);
            //$user->allimages()->save($imageable);


            // delete actual tracking
            // Videocall::where('beamer_id', $user->id)->forceDelete();
            // UserTrack::where('user_id', $user->id)->forceDelete();
            UserTrack::where('user_id', $user->id)->update(['last_one' => 0]);
            UserTrack::where('user_id', $user->id)->delete();
            UserTracking::where('user_id', $user->id)->forceDelete();

            // get random europe city, coordinates and languages
            $local = $capitais[array_rand($capitais)];
            $langs = [[ 'lang_code'=>$local['idioma'] ]];
            $user->save_langs($langs);

            // making a variation of lat and lng in 0.04 degrees
            $variant1 = random_int(-40, 40) / 1000;
            $lat = $local['latitude'] + $variant1;
            $variant2 = random_int(-40, 40) / 1000;
            $lng = $local['longitude'] + $variant2;

            $to_save = [
                'city'=>$local['cidade'],
                'country'=>$local['pais'],
                'address_type'=>'store'
            ];
            $wa = new WorldAddress($local['cidade'] . ', ' . $local['pais']);
            $latlng=$wa->request_lat_lng();
            if(isset($latlng['lat'])){
                $to_save =  array_merge($to_save, $latlng);
            }

            \DB::table('user_addresses')->where('user_id', $user->id)->delete();
            $user->address()->create($to_save);

            $beamer_type = config('thisapp.beamer_type');
            $with_donation = 0;
            $is_freemium = 0;

            if($type == 'instore'){
                $cost_per_minute = 0;
            } else {
                $cost_per_minute = 2.07;
            }

            $categories = $this->get_categories();
            $categories = ':'.implode(':', $categories).':';

            $event_title = $eventos[array_rand($eventos)] . ' :test';


            $track_atual = UserTrack::create([
                    'user_id'=>$user->id,
                    'status'=>'on',
                    'last_one'=>1,
                    'beamer_type' => $beamer_type,
                    'cost_per_minute' => $cost_per_minute,
                    'event_title' => $event_title,
                    'categories' => $categories,
                    'with_donation' => $with_donation,
                    'is_freemium' => $is_freemium,
                    'lat'=>$lat,
                    'lng'=>$lng,
                ]);
            UserTracking::create([
                'user_id'=>$user->id,
                'status'=>'start',
                'beamer_type' => $beamer_type,
                'lat'=>$lat,
                'lng'=>$lng,
            ]);


            // acct_1NBP0C2eKu3NL3Uj	1
            // insert or update the one to one relationship in laravel orm
            $user->beamer()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'account_id'=>'acct_1NBP0C2eKu3NL3Uj',
                    'account_stripe_enabled' => 1,
                    'account_token' => ''
                ]
            );
            $user->shopper()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'shopper_enabled' => 1,
                ]
            );

            // random if user have a teaser video
            // setting a video teaser
            $has_video = true; // random_int(0, 100) > 80 ? false : true;


            $teaser_text_size =
                random_int(0, 100) > 70 ?
                random_int(30, 70) : // short texts between 30 and 60 characters
                random_int(300, 600); // middle text between 300 and 600 characters


            // $user->videofeed()->forceDelete();
            if($has_video){
                $user_video = array_pop($videos);
                $user_video['user_id'] = $user->id;
                // $user_video['teaser_text'] = array_pop($promocoes);
                $user_video['teaser_text'] = "test: " . fake()->text($teaser_text_size);
                $user_video['teaser_style'] = '';
                UserVideofeed::create($user_video);
            }

            $online = array_pop($off_on);
            // if($online=='offline'){
            //     $track_atual->status = 'end';
            //     $track_atual->save();
            //     $this->info('User ' . $user->id . ' is offline now.');
            // } else {
                $this->info('User ' . $user->id . ' is online now.');
            // }
        }

        $this->info('Done');
        return 0;
    }
}
