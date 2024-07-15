<?php
namespace VideoRobot;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class HttpClient {

	private $cli;

    private $options = [
        'headers' => [
            'Accept'     => 'application/json'
        ]
    ];

	public function __construct($client = null){
        if(empty($client)){
            $this->cli = new Client();
        } else {
            $this->cli = $client;
        }
	}

    public function baerer($value){
        $this->options['headers']['Authorization'] = 'Bearer ' . $value;
    }

	public function get($url){
		$response = $this->cli->request('GET', $url, $this->options);
		return json_decode($response->getBody());
	}

    public function touch($url){
        $success = false;
        try {
            $response = $this->cli->request('GET', $url);
            $success = json_decode($response->getBody());
        } catch(RequestException $e){
            $success = false;
        } catch (\Exception $e){
            $success = false;
        }
        return $success;
    }

    public function postfile($url, $file){
        $opt = [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => Psr7\Utils::tryFopen($file, 'r')
                ]
            ]
        ];
        $head = $this->options + $opt;
        $response = $this->cli->request('POST', $url, $head);
        return json_decode($response->getBody());
    }

}
