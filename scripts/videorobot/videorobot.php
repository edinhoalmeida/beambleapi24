<?php
require 'vendor/autoload.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

use Dotenv\Dotenv;

use VideoRobot\VideoService;
use VideoRobot\DbService;

Dotenv::createImmutable(__DIR__)->load();
function _env($key, $default = null)
{
    return !empty($_ENV[$key]) ? $_ENV[$key] : $default ;
}

class VideoRobot extends CLI
{
    private $VS;
    private $DB;
    private $videos_folder;
    private $videos_extensions;

    protected function setup(Options $options)
    {
        $options->setHelp('Do the upload of new video to an server service');
        $options->registerOption('version', 'print version', 'v');
        $options->registerOption('run', 'run upload', 'r');
        $options->registerOption('daemon', 'run as daemon (in loop)', 'd');
        $options->registerOption('fix', 'fix', 'f');
        $options->registerOption('list', 'list', 'l');

        $this->videos_folder = dirname(dirname(__DIR__)) . '/' . _env('VIDEOS_FOLDER');
        $this->videos_extensions = _env('VIDEOS_EXT');

        $this->DB = new DbService();
        $this->VS = new VideoService();
    }

    // implement your code
    protected function main(Options $options)
    {
        if ($options->getOpt('version')) {
            $this->info(' 0.1 using bytescale.com');
        } elseif ($options->getOpt('run')) {
            $this->info(' running once');
            $this->loop();        
        } elseif ($options->getOpt('daemon')) {
            $this->info(' running daemon');
            $this->loop(true);        
        } elseif ($options->getOpt('fix')) {
            $this->DB->fix_url_thumbs();     
        } elseif ($options->getOpt('list')) {
            $files_on_service = $this->VS->list_files();
            $videos_files = glob($this->videos_folder . "/" . $this->videos_extensions, GLOB_BRACE);
            $all_files = [];
            foreach ($videos_files as $file) {
                if (is_file($file) && strpos($file, '_optimized')===false) {
                    if($jid !== false){
                        $all_files[] = ['file'=>$file,'jid'=>$jid];
                    }
                }
            }
            $files_erase = [];
            foreach($files_on_service as $fos){
                $full_path =  $fos;
                if(strpos($full_path,'_optimized')!==false){
                    $files_erase[] = $full_path;
                    continue;
                }
                $p = explode("/", $fos);
                $file = end( $p );
                $p = explode("-", $file);
                $file = end( $p  );
                // check in db is is used
                // TODO erase unused files
                echo "TODO erase unused files???????????\n";
                echo $file . "\n";
            }
        } else {
            echo $options->help();
        }
    }

    protected function loop($daemon = false){

        $videos_files = glob($this->videos_folder . "/" . $this->videos_extensions, GLOB_BRACE);
        $all_files = [];
        foreach ($videos_files as $file) {
            if (is_file($file) && strpos($file, '_optimized')===false) {
                $jid = $this->DB->add_file($file);
                if($jid !== false){
                    $all_files[] = ['file'=>$file,'jid'=>$jid];
                }
            }
        }
        if(empty($all_files)){
            $this->check_thumb();
            if($daemon){
                $this->delayloop($daemon);
            }
            return;
        } else {
            $this->notice(' total files: ' . count($all_files));
        }
        
        // $this->DB->erase_all();
        $convert_number = 30;
        foreach ($all_files as $fileo) {
            $file = $fileo['file'];
            $jid = $fileo['jid'];
            // send to service
            $service_return = $this->VS->send_file($file);

            if(!empty($service_return->files))
            // if(!empty($service_return->files[0]->fileUrl))
            {
                $service_file = $service_return->files[0];
                $this->DB->update(
                    $jid, 
                    json_encode($service_return), 
                    $service_file->filePath,
                    $service_file->fileUrl
                );

                $urls_data = $this->VS->convert_file($service_file->fileUrl);
                $this->DB->update_urls($jid, $urls_data);

            } else {
                $service_file = $service_return->files[0];
                $this->DB->update(
                    $jid, 
                    json_encode($service_return), 
                    null,
                    null
                );
            }
            // convert 
            $convert_number--;
            if($convert_number<=0){
                break;
            }
        }

//    https://upcdn.io/W142hJk/video/example.mp4?h=1080
//   "errors": [
//     {
//       "error": {
//         "code": "error_code",
//         "message": "Error message."
//       },
//       "formDataFieldName": "file"
//     }
//   ],
//   "files": [
//     {
//       "accountId": "W142ifv",
//       "etag": "33a64df551425fcc55e4d42a148795d9f25f89d4",
//       "filePath": "/uploads/file.txt",
//       "fileUrl": "https://upcdn.io/A623uY2/raw/uploads/file.txt",
//       "formDataFieldName": "file"
//     }
//   ]
        $this->check_thumb();
        
        if($daemon){
            $this->delayloop($daemon);
        }
    }

    protected function delayloop($daemon){
        sleep(1);
        $this->loop($daemon);
    }


/**

{
  "jobUrl": "https://api.bytescale.com/v2/accounts/W142ifv/jobs/ProcessVideoJob/01J1B29AQHS4SW8SM9G0NCF0R3",
  "jobDocs": "https://www.bytescale.com/docs/job-api/GetJob",
  "jobId": "01J1B29AQHS4SW8SM9G0NCF0R3",
  "jobType": "ProcessVideoJob",
  "accountId": "W142ifv",
  "created": 1719432162034,
  "error": {
    "code": "cannot_determine_input_dimensions",
    "message": "Unable to determine input video dimensions."
  },
  "lastUpdated": 1719432162606,
  "status": "Failed",
  "summary": {

  }
}


{
  "jobUrl": "https://api.bytescale.com/v2/accounts/W142hJk/jobs/ProcessFileJob/01H3211XMV1VH829RV697VE3WM",
  "jobDocs": "https://www.bytescale.com/docs/job-api/GetJob",
  "jobId": "01H3211XMV1VH829RV697VE3WM",
  "jobType": "ProcessFileJob",
  "accountId": "W142hJk",
  "created": 1686916626075,
  "lastUpdated": 1686916669389,
  "status": "Succeeded",
  "summary": {
    "result": {
      "type": "Artifact",
      "artifact": "/video.mp4",
      "artifactUrl": "https://upcdn.io/W142hJk/video/example.mp4!f=mp4-h264&a=/video.mp4"
    }
  }
}


*/
    protected function check_thumb(){
        $files_pending = $this->DB->get_uploads_pending();
        $check_number = 50;
        $this->info(' check_thumb');
        foreach ($files_pending as $file) {
            $this->info(' file countdown' . $check_number);
            $this->DB->update_attempts($file['id'], (1 + (int) $file['attempts']));
            foreach(['url_video','url_thumb'] as $column){
                $jobdata = $this->VS->test_url($file[$column]);                
                if(!empty($jobdata->status) && $jobdata->status=='Failed'){
                    $this->DB->update_status(
                        $file['id'],
                        $column . ":" . $jobdata->status,
                        json_encode( (array) $jobdata)
                    );
                }
                if(!empty($jobdata->status) && $jobdata->status=='Pending'){
                    $this->DB->update_status(
                        $file['id'],
                        $column . ":" . $jobdata->status,
                        json_encode( (array) $jobdata)
                    );
                }
                if(!empty($jobdata->status) && $jobdata->status=='Succeeded' && !empty($jobdata->summary->result->artifactUrl)){
                    $this->DB->update_status(
                        $file['id'],
                        $column . ":" . $jobdata->status,
                        json_encode( (array) $jobdata),
                        ($column=='url_video')? 'succeeded' : null
                    );
                    $this->DB->update_one_url($file['id'], $column,  $jobdata->summary->result->artifactUrl);
                }
                if(!empty($jobdata->error)){
                    $this->DB->update_status(
                        $file['id'],
                        $column . ":" . $jobdata->error->code,
                        $jobdata->error->message,
                        'failed',
                    );
                }
            }
            // convert 
            $check_number--;
            if($check_number<=0){
                break;
            }
        }
    }
}
/*
debug
info
notice
success (this is not defined in PSR-3)
warning
error
critical
alert
emergency
*/
// execute it
$cli = new VideoRobot();
$cli->run();
exit;