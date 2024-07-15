<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

/**
 * @OA\Info(
 *     title="Beamble API V2",
 *     version="0.2",
 *     description="* 🔒 = routes or tags that requires authentication (by baerer)",
 * ),
 * @OA\Server(url="http://beamble20.local/api"),
 * @OA\Server(url="https://apibb.beamble.com/api")
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     name="bearerAuth",
 *     in="header",
 *     securityScheme="api_key"
 * )
 */
class BaseController extends Controller
{

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 400)
    {

        // 404 Not Found
        // 401 Unauthorized
        // 403 Forbidden
        // 400 Bad Request

        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}
