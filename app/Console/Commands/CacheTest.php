<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\UserTracking;
use App\Models\UserTrack;
use App\Models\Videocall;
use App\Models\ImageB64;
use App\Models\UserVideofeed;
use App\Models\UserFollowers;

use Illuminate\Support\Benchmark;

use Illuminate\Support\Facades\Cache;

class CacheTest extends Command
{
    protected $signature = 'cachetest';

    protected $users_id = [];

    protected $description = 'test of cached/not cached time';

    public static $users;
    public static $actual;

    public function handle()
    {
        
        $t1 = Benchmark::measure(fn () => CacheTest::$users = UserTrack::withTrashed()->select('user_id')->where('user_id', '>', 85)->distinct()->limit(10)->get() );
        $this->info('Select users');
        echo 'T1: ' .  $t1 . 'ms' . PHP_EOL;
        foreach (CacheTest::$users as $u) {
            $this->users_id[ $u->user_id ] = $u->user_id;
        }

        // get
        $this->info('Get time of profiles:');

        $total_time = 0;
        foreach ( $this->users_id as $u_id) {
            CacheTest::$actual =  $u_id;
            $t2 = Benchmark::measure(fn () => CacheTest::get(CacheTest::$actual) );
            $total_time += $t2;
            echo $u_id . ' :T2: ' .  $t2 . 'ms' . PHP_EOL;
        }

        $this->info('Total');

        echo 'Total: ' .  $total_time . 'ms' . PHP_EOL;

        // get
        $this->info('Get cached of profiles:');

        $total_time = 0;
        foreach ( $this->users_id as $u_id) {
            CacheTest::$actual = "ch2" . md5($u_id);
            $t2 = Benchmark::measure(fn () => Cache::get(CacheTest::$actual) );
            $total_time += $t2;
            echo $u_id . ' :T3: ' .  $t2 . 'ms' . PHP_EOL;
        }

        $this->info('Total');

        echo 'Total: ' .  $total_time . 'ms' . PHP_EOL;

        CacheTest::$actual =  implode(",", array_values($this->users_id));
        $t3 = Benchmark::measure(fn () => CacheTest::post(CacheTest::$actual) );
        echo 'Post all profile :T3: ' .   $t3 . 'ms' . PHP_EOL;
       
        $this->info('Done');
        return 0;
    }

    public static function get($id){
        $ch = curl_init();
        $url = env('APP_URL') . '/api/profile/' . $id;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        if ($erro = curl_errno($ch)) {
            dd($erro);
        }
        $responsejson = json_decode($response);
        curl_close($ch);
        $chave = "ch2" . md5($id);
        $cachereturn = Cache::put($chave, $response , 300); // 5 Minutes
        return $responsejson;
    }

    public static function post($postData){
        $ch = curl_init( env('APP_URL') . '/api/profilemany');
            curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                // 'Authorization: apikey '.$authToken,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
            ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}
