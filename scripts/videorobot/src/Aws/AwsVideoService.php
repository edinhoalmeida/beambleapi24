<?php
namespace VideoRobot\Aws;

use VideoRobot\HttpClient;
use Aws\S3\S3Client;

class AwsVideoService {

	private $name = 'aws media converter';
	private $httpCli;

	private $base_url;
	private $base_account;
	private $base_secret;

	public function __construct($httpClient = null){
		if(empty($httpClient)){
			$this->httpCli = new HttpClient();
		} else {
			$this->httpCli = $httpClient;
		}
		$this->base_url = _env('BYTESCALE_BASE_URL');
		$this->base_account = _env('BYTESCALE_ACCOUNT');
		$this->base_secret = _env('BYTESCALE_SECRET');
		$this->httpCli->baerer($this->base_secret);
	}

	public function send_file($file)
	{
		$url = $this->base_url . "/accounts/". $this->base_account ."/uploads/form_data";
		$response = $this->httpCli->postfile($url, $file);
		return $response;
	}

	public function list_files($path = '/uploads', $all_files = [])
	{

        //Create a S3Client
//        $s3Client = new S3Client([
//            'profile' => 'default',
//            'region' => 'us-west-2',
//            'version' => '2006-03-01'
//        ]);

//Listing all S3 Bucket
//        $buckets = $s3Client->listBuckets();
//        foreach ($buckets['Buckets'] as $bucket) {
//            echo $bucket['Name'] . "\n";
//        }
		$url = $this->base_url . "/accounts/". $this->base_account ."/folders/list?folderPath=" . $path;
		// print_r($url);
		$response = $this->httpCli->get($url);

		$folders_to_search = [];

		if(!empty($response->items)){
			foreach ($response->items as $item) {
				if(!empty($item->type) && $item->type=='Folder')
				{
					$folders_to_search[] = $item->folderPath;
				} else if(!empty($item->type) && $item->type=='File')
				{
					$all_files[] = $item->filePath;
				} else {
					// print_r($item);
				}
			}
		}
		foreach($folders_to_search as $folder){
			// echo $folder ."\n";
			$all_files = $this->list_files($folder, $all_files);
		}

		// print_r($all_files);

		return $all_files;
	}

}
