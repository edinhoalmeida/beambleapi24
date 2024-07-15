<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\UserVideofeed;


use DB;

class CleanVideos extends Command
{

    protected $signature = 'cleanvideos';

    protected $description = 'Erase deleted videos and thumbs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        // clean user erased:
        $users_universe = [
            "SELECT b.id FROM `users` b WHERE b.email LIKE '%eraseAccount%'",
            "SELECT b.id FROM `users` b WHERE b.is_generic = '1'",
        ];
        foreach($users_universe as $sub_user_id){
            $this->info('clear user: ');
            \DB::update("update user_videofeed set deleted_at=NOW() where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from users_track_logs where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from users_track where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_followers where user_id IN( {$sub_user_id} ) OR follower_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_lang where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_products where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_shopper where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_stripe_account where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_stripe_customer where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_stripe_ephemeral where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_stripe_objects where user_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_user_products where client_id IN( {$sub_user_id} )");
            \DB::delete("delete from user_user_products where beamer_id IN( {$sub_user_id} )");
            \DB::delete("delete from videocalls where beamer_id IN( {$sub_user_id} )");
            \DB::delete("delete from videocalls where client_id IN( {$sub_user_id} )");
            \DB::delete("delete from personal_access_tokens where tokenable_id IN( {$sub_user_id} )");
            \DB::delete("delete from model_has_roles where model_id IN( {$sub_user_id} )");
        }
        \DB::delete("delete from users where email LIKE '%eraseAccount%'");
        \DB::delete("delete from users where is_generic = '1'");


        $this->newLine(2);
        $trashed = UserVideofeed::onlyTrashed()->get();
        $base_path = self::urlTest('storage','storage_tmp');

        foreach ($trashed as $video_feed) {
            // outro user robo copiaou este video pra si
            $alguem_usando = UserVideofeed::where('original', $video_feed->original)->first();
            if(!empty($alguem_usando)){
                $this->info('alguem copiou:'. $video_feed->original);
                $this->newLine(1);
                $video_feed->forceDelete();
                continue;
            }
            if(is_file($base_path . DIRECTORY_SEPARATOR . $video_feed->thumb)){
                unlink($base_path . DIRECTORY_SEPARATOR . $video_feed->thumb);
            }
            if(is_file($base_path . DIRECTORY_SEPARATOR . $video_feed->converted)){
                unlink($base_path . DIRECTORY_SEPARATOR . $video_feed->converted);
            }
            if(is_file($base_path . DIRECTORY_SEPARATOR . $video_feed->original)){
                unlink($base_path . DIRECTORY_SEPARATOR . $video_feed->original);
            }
            $this->info('Video id:' . $video_feed->id);
            $video_feed->forceDelete();
            $this->newLine(1);
        }


        // now looking for files without feed db line
        $videos_files = glob($base_path . DIRECTORY_SEPARATOR . '*.{mp4,mov}', GLOB_BRACE);
        $all_files = [];
        foreach ($videos_files as $file) {
            if (is_file($file) && strpos($file, '_optimized')===false) {
                $pieces = explode('/',$file);
                $all_files[] = end($pieces);
            }
        }
        foreach ($all_files as $file) {
            $alguem_usando = UserVideofeed::where('original', $file)->first();
            if (empty($alguem_usando)) {
                $pieces = explode('.',$file);
                $inicio = $pieces[0];
                echo "ninguém usando o arquivo: " . $file . "\n";
                $videos_files3 = glob($base_path . DIRECTORY_SEPARATOR . $inicio . '*', GLOB_BRACE);
                foreach ($videos_files3 as $file3) {
                    echo "======" . $file3 . "\n";
                    if(is_file($file3)){
                        unlink($file3);
                    }
                }
            }
        }
        // $sync_script =  'sync_aws_s3.sh';
        // $bsync = self::urlTest('scripts', $sync_script);
        // $output = shell_exec($bsync . ' -d '.$base_path.' 2>&1');
        // $this->newLine(2);
        return 0;
    }

    public static function urlTest(...$segments){
        if(!empty($_SERVER['BASE_OF_LARAVEL'])){
            $base_path = $_SERVER['BASE_OF_LARAVEL'];
        } else {
            $base_path = dirname( dirname( dirname( __FILE__ ) ) );
        }
        array_unshift($segments, $base_path);
        return join(DIRECTORY_SEPARATOR, $segments);
    }
}
