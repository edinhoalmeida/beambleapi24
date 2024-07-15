<?php 
namespace App\Service;

use App\Models\UserVideofeed;

use Illuminate\Support\Facades\Storage;

class VideoProcessor implements VideoProcessorInterface
{
    public function converVideo(UserVideofeed $uservideofeed){

        $original = $uservideofeed->original;

        $destiny = preg_replace('@^(.*)\.[a-zA-Z0-9]+$@', '${1}_optimized.mp4', $original);
        $thumb = preg_replace('@^(.*)\.[a-zA-Z0-9]+$@', '${1}_thumb.jpg', $original);

        $id = $uservideofeed->id;

        $service_script = config('videoconvert.service');

        $bs = self::urlTest('scripts', $service_script);

        $base_path = self::urlTest('storage', 'storage_tmp');

        $command_input = $bs . ' -b '.$base_path.' -n '.$original.''.' -d '.$destiny.''.' -t '.$thumb;

        dblog('video converter', $command_input);
        $uservideofeed->service = $service_script; 
        $uservideofeed->command_input = $command_input; 
        $uservideofeed->save();

        $output = shell_exec($bs . ' -b '.$base_path.' -n '.$original.''.' -d '.$destiny.''.' -t '.$thumb.' 2>&1');

        dblog('video converter executou', "...");

        $original_file = self::urlTest('storage','storage_tmp', $original);
        if (file_exists($original_file)) {
            $original_size = filesize($original_file);
            $uservideofeed->original_size = $original_size;
        } 
        
        $converted_file = self::urlTest('storage','storage_tmp', $destiny);
        if (file_exists($converted_file)) {
            $converted_size = filesize($converted_file);
            $uservideofeed->converted = $destiny;
            $uservideofeed->converted_size = $converted_size;
            $uservideofeed->command_output = '';
        } else {
            $uservideofeed->command_output = $output;
        }

        $thumb_file = self::urlTest('storage','storage_tmp', $thumb);
        if (file_exists($thumb_file)) {
            $uservideofeed->thumb = $thumb;
        } 

        $uservideofeed->save();

        $sync_script =  'sync_aws_s3.sh';
        $bsync = self::urlTest('scripts', $sync_script);
        $output = shell_exec($bsync . ' -d '.$base_path.' 2>&1');
        dblog('video converter enviando para S3', "...");
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

