<?php
namespace Shipping\Drivers;

define('UBER_APP_ID', getenv('UBER_APP_ID'));
define('UBER_SECRET', getenv('UBER_SECRET'));
define('UBER_WEBHOOK_KEY', getenv('UBER_WEBHOOK_KEY'));
define('UBER_BASE_URL', getenv('UBER_BASE_URL'));

use Shipping\Contract\Contract as ShippingContract;
use Shipping\Exceptions\NotImplemented as ShippingNotImplemented;

use Illuminate\Support\Facades\Http;

class UberClient implements ShippingContract {
/**
$response->ok() : bool;                  // 200 OK
$response->created() : bool;             // 201 Created
$response->accepted() : bool;            // 202 Accepted
$response->noContent() : bool;           // 204 No Content
$response->movedPermanently() : bool;    // 301 Moved Permanently
$response->found() : bool;               // 302 Found
$response->badRequest() : bool;          // 400 Bad Request
$response->unauthorized() : bool;        // 401 Unauthorized
$response->paymentRequired() : bool;     // 402 Payment Required
$response->forbidden() : bool;           // 403 Forbidden
$response->notFound() : bool;            // 404 Not Found
$response->requestTimeout() : bool;      // 408 Request Timeout
$response->conflict() : bool;            // 409 Conflict
$response->unprocessableEntity() : bool; // 422 Unprocessable Entity
$response->tooManyRequests() : bool;     // 429 Too Many Requests
$response->serverError() : bool;         // 500 Internal Server Error

 */
    public static $client = null;

    public static function get($url){
        $url = UBER_BASE_URL . UBER_APP_ID . '/' . $url;
        $response = Http::withToken(UBER_SECRET)->acceptJson()->get($url);
        return $response;
    }

    public static function post($url, $data){
        $url = UBER_BASE_URL . UBER_APP_ID . '/' . $url;
        $response = Http::withToken(UBER_SECRET)->acceptJson()->post($url, $data);
        return $response;
    }

    public static function shipping_details(int $beamer_id, int $client_id){

        // if( ! is_null(self::$client) ){
        //     return self::$client;
        // }
        // self::$client = [
        //     'shipping'=>'user',
        //     'beamer_id'=>$beamer_id,
        //     'client_id'=>$client_id,
        // ];
        // return self::$client;

        $return_ok = [
            "kind"=>"delivery_quote",
            "id"=>"dqt_6gQ2dt31TjiOPfwux-NCXg",
            "created"=>"2023-04-06T19:00:37.887Z",
            "expires"=>"2023-04-06T19:15:37.887Z",
            "fee"=>array_rand(range(800, 2400)),
            "currency"=>"usd",
            "currency_type"=>"USD",
            "dropoff_eta"=>"2023-04-06T19:34:22.000Z",
            "duration"=>33,
            "pickup_duration"=>24,
            "dropoff_deadline"=>"2023-04-06T20:09:20.000Z"
        ];
        return $return_ok;

        
        $data = [
            "pickup_address"=>[
                "street_address"=>["100 Maiden Ln"],
                "city"=>"New York",
                "state"=>"NY",
                "zip_code"=>"10023",
                "country"=>"US"
            ],
            "dropoff_address"=>[
                "street_address"=>["30 Lincoln Center Plaza"],
                "city"=>"New York",
                "state"=>"NY",
                "zip_code"=>"10023",
                "country"=>"US"
            ],
            "pickup_latitude"=>40.7066581,
            "pickup_longitude"=>-74.0071868,
            "dropoff_latitude"=>38.9298375,
            "dropoff_longitude"=>-77.0582303,
            "pickup_phone_number"=>"+15555555555",
            "dropoff_phone_number"=>"+15555555555",
            "manifest_total_value"=>1000,
            "external_store_id"=>"from:".$beamer_id.",to:".$client_id
        ];
        $response = self::post('delivery_quotes', $data);
        if($response->ok()){
            return $response->json();
        } else {
            self::$client = [
                'shipping'=>'user',
                'request_status'=>$response->status(),
                // 'request_reason'=>$response->reasonPhrase,
                'beamer_id'=>$beamer_id,
                'client_id'=>$client_id,
            ];
            return self::$client;
        }
    }

}