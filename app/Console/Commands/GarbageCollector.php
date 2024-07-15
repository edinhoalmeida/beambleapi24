<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\UserDevices;
use App\Libs\FirebasePN;


class GarbageCollector extends Command
{

    protected $signature = "garbagecollector";

    protected $description = "Rotine of the garbage collector";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->newLine(2);
        $this->info("================================");
        $this->info("Firebase testing tokens");
        $firebase_tokens = UserDevices::select("firebase_token")->withTrashed()->distinct()->pluck("firebase_token")->all();
        $FB = new FirebasePN();
        $tested = $FB->validate_tokens($firebase_tokens);
        $to_exclude = [];
        foreach ($tested as $status=>$tokens){
            if($status=="valid"){
                continue;
            }
            if($status=="unknown" || $status=="invalid")
            {
                $to_exclude = array_merge($to_exclude, $tokens);
            }
        }
        if(empty($to_exclude)){
            $this->info("-- All Tokens are valid");
        } else {
            $this->info("-- Tokens to exclude:");
            dump($to_exclude);
            UserDevices::whereIn("firebase_token", $to_exclude)->forceDelete();
            $FB->remove_users_from_topic($to_exclude);
        }
        $this->newLine();
        $this->info("Done");
        $this->newLine(2);

        return 0;
    }

}
