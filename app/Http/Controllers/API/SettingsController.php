<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

use App\Libs\Videosdk;
use App\Models\Category;


class SettingsController extends Controller
{

/**
    * @OA\Get(
    *     path="/settings",
    *     tags={"settings"},
    *     @OA\Response(
    *          response="200",
    *          description="Get app general settings",
    *          @OA\JsonContent(
    *                  @OA\Property(
    *                      property="success",
    *                      type="boolean",
    *                  ),
    *                  @OA\Property(
    *                      property="data",
    *                      type="object",
    *                      @OA\Property(
    *                          property="video_sdk_token",
    *                          type="string",
    *                          description="Key to video sdk api",
    *                      ),
    *                      @OA\Property(
    *                          property="languages_list",
    *                          type="string",
    *                          description="Languages avaiable",
    *                      ),
    *                      @OA\Property(
    *                          property="categories",
    *                          type="array",
    *                          description="All categories",
    *                          @OA\Items(ref="#/components/schemas/categoryshort") 
    *                      ),
    *                  ),
    *                  @OA\Property(
    *                      property="messages",
    *                      type="array",
    *                      nullable=true,
    *                      @OA\Items(),
    *                  )
    *          ),
    *     )
    * )
    */
    public function get_settings()
    {
        // $conf_maps = config('maps');
        $categories = Category::get_all();

        $response = [
            'success' => true,
            'data'    => [
                // 'maps_api_key' => $conf_maps['key'],
                'video_sdk_token' => Videosdk::get_videosdk_token(),
                'languages_list' => config('language.languages_to_json'),
                'categories' => $categories
            ],
            'messages' => []
        ];
        return response()->json($response, 200);
    }

    private function get_videosdk_token(){

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

}
