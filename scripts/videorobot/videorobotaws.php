<?php
require 'vendor/autoload.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

use Dotenv\Dotenv;

use VideoRobot\Aws\AwsDbService;
use VideoRobot\Aws\AwsVideoService;

use VideoRobot\Websockets;

Dotenv::createImmutable(__DIR__)->load();
function _env($key, $default = null)
{
    return !empty($_ENV[$key]) ? $_ENV[$key] : $default ;
}

class VideoRobotAws extends CLI
{
    private $VS;
    private $DB;
    private $videos_folder;
    private $videos_extensions;
    private $input_s3;
    private $output_s3;
    private $credentials;
    private $job_json;

    protected function setup(Options $options)
    {
        $options->setHelp('Do the upload of new video to an server service');
        $options->registerOption('version', 'print version', 'v');
        $options->registerOption('run', 'run upload', 'r');
        $options->registerOption('daemon', 'run as daemon (in loop)', 'd');
        $options->registerOption('fix', 'fix', 'f');
        $options->registerOption('list', 'list', 'l');
        $options->registerOption('websockets', 'websockets', 'w');



        $this->videos_folder = dirname(dirname(__DIR__)) . '/' . _env('VIDEOS_FOLDER');
        $this->videos_extensions = _env('VIDEOS_EXT');
        $this->input_s3 = _env('AWS_S3_INPUT');
        $this->output_s3 = _env('AWS_S3_OUTPUT');
        $this->credentials = ''; # " --profile " . __DIR__ . "/.aws/credentials";
        $this->job_json = file_get_contents(__DIR__ . '/template.json');
        $this->job_json = json_decode($this->job_json, true);
        $this->DB = new AwsDbService();
        $this->VS = new AwsVideoService();
    }

    // implement your code
    protected function main(Options $options)
    {
        if ($options->getOpt('version')) {
            $this->info(' 0.1 using AWS Media Convert');
        } elseif ($options->getOpt('run')) {
            $this->info(' running once');
            $this->loop();
        } elseif ($options->getOpt('daemon')) {
            $this->info(' running daemon');
            $this->loop(true);
        } elseif ($options->getOpt('websockets')) {
            $this->info(' running websockets');
            $this->websockets_server();
        } else {
            echo $options->help();
        }
    }

    protected function loop($daemon = false){

        // procura por video em estado new
        $new_videos = $this->DB->has_video_new('new');
        foreach ($new_videos as $video) {
            $file_complete = $this->videos_folder . '/' . $video['file'];
            $this->DB->new_status($video['id'], '0 upload starts', '');
            $output = "ja foi"; #shell_exec("aws s3 cp $file_complete " . $this->input_s3 . $this->credentials .' 2>&1');
            $this->DB->new_status($video['id'], '1 upload ends', (string) $output);
            $this->DB->update($video['id'], 'uploaded');
        }

        // https://docs.aws.amazon.com/cli/latest/reference/mediaconvert/create-job.html
        $uploaded_videos = $this->DB->has_video_new('uploaded');
        foreach ($uploaded_videos as $video) {
            $this->change_json_and_save($this->job_json, $video['file'], $this->input_s3. $video['file'], $this->output_s3);
            $token = md5($video['file'].$video['id']."tokenizer");
            $params = [];
            $params[] = "--cli-input-json file://" . __DIR__ . '/jobs/' . $video['file'] . '.job.json';
            $params[] = "--region eu-west-3";
//            $params[] = "--client-request-token " . $token;
            $this->DB->update_token($video['id'], $token);
            $output = shell_exec("aws mediaconvert create-job " . implode(" ", $params) . ' ' . $this->credentials .' 2>&1');
            $this->DB->new_status($video['id'], '2 job converter', (string) $output);
//            $this->DB->update($video['id'], 'job_created');
        }

        if($daemon){
            $this->delayloop($daemon);
        }
    }

    protected function delayloop($daemon){
        sleep(1);
        $this->loop($daemon);
    }

    protected function change_json_and_save($json, $file, $input, $output)
    {
        $json['Settings']['OutputGroups'][0]['OutputGroupSettings']['HlsGroupSettings']['Destination']=$output;
        $json['Settings']['Inputs'][0]['FileInput']=$input;
        $str_json = json_encode($json, JSON_PRETTY_PRINT |  JSON_UNESCAPED_SLASHES);
        $json_path = __DIR__ . '/jobs/' . $file . '.job.json';
        file_put_contents($json_path, $str_json);
        return $json_path;
    }

    protected function websockets_server()
    {
        $ws = new Websockets();
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
$cli = new VideoRobotAws();
$cli->run();
exit;
/*

set_time_limit (0);

$address = '46.49.41.188';

$port = 7777;
$con = 1;
$word = "";

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
$bind = socket_bind($sock, $address, $port);

socket_listen($sock);

while ($con == 1)
{
    $client = socket_accept($sock);
    $input = socket_read($client, 2024);

    if ($input == 'exit')
    {
        $close = socket_close($sock);
        $con = 0;
    }

    if($con == 1)
    {
        $word .= $input;
    }
}

echo $word;
 *
 */
