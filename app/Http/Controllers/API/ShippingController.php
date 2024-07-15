<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use Shipping;

class ShippingController extends BaseController
{

    public function test(Request $request)
    {
        $shipping = Shipping::create_shipping_estimate(18, 19);
        return response()->json($shipping, 200);
    }

    public function postWebhook(Request $request)
    {
        // todo implet header verification
        // whsec_9yOmexpjAEafbwQEGAHydKOAB1aACQ8l
        $dado = $request->all();
        $webHook_proccess = Shipping::webHook($dado);
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }
}
