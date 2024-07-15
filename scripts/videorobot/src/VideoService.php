<?php
namespace VideoRobot;

use VideoRobot\HttpClient;

class VideoService {

	private $name = 'bytescale.com';
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

	public function convert_file($fileUrl)
	{
		$query_opt = [
			'w'=>720,
			'h'=>1280,
			// 'f'=>'hls-h264',
			'f'=>'mp4-h264',
			'fit'=>'crop',
			'crop'=>'center',
			'te'=>180, // time end
		];
		$url_video = str_replace('raw', 'video', $fileUrl) . "?" . http_build_query($query_opt);

		// $response = $this->httpCli->touch($url_video);

		$query_opt = [ 
			'w'=>720,
			'h'=>1280,
			'fit'=>'crop',
			'crop'=>'center',
			't'=>1 // time start of thumb
		];
		$url_thumb = str_replace('raw', 'image', $fileUrl) . "?" . http_build_query($query_opt);
		
		// $response = $this->httpCli->touch($url_thumb);
		
		return [
			'url_video'=>$url_video,
			'url_thumb'=>$url_thumb
		];
	}

	public function test_url($url){
		$response = $this->httpCli->touch($url);
		return $response;
	}

}
