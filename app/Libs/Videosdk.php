<?php 
namespace App\Libs;

use Firebase\JWT\JWT;

class Videosdk {

    public static function get_videosdk_token(){

        $conf_videosdk = config('videosdk');

        $issuedAt = new \DateTimeImmutable();
        $expire = $issuedAt->modify('+24 hours')->getTimestamp();

        $payload = (object)[];

        $payload->apikey = $conf_videosdk['key'];
        $payload->permissions = array(
            "allow_join",
            "allow_mod"
        );
        $payload->iat = $issuedAt->getTimestamp();
        $payload->exp = $expire;

        $payload = (array) $payload;

        $jwt = JWT::encode($payload, $conf_videosdk['secret'], 'HS256');

        return $jwt;

    }

    public static function create_meeting(){

        $conf_videosdk = config('videosdk');

        $token = self::get_videosdk_token();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $conf_videosdk['api_endpoint'] . '/rooms',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_FAILONERROR => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token,
                'Content-Type: application/json'
            ) ,
        ));

        $response = curl_exec($curl);

        if($response==false){
            $er = curl_error($curl);
            curl_close($curl);
            return $er;
        }
        curl_close($curl);
        return $response;
    }


}