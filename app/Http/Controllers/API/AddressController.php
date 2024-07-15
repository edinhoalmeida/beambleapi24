<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

use App\Libs\WorldAddress;

class AddressController extends Controller
{

    public function get_fmt(Request $request)
    {
        if( $request->has('address_components') ){                
            $dados = $request->get('address_components');
            $dados = json_decode($dados, null, 512, JSON_OBJECT_AS_ARRAY);
        } else {
            $dados = $request->all();
        }
        $fmt_address = WorldAddress::getAddressFmt($dados);
        $response = [
            'success' => true,
            'data'    => [
                'fmt_address' => $fmt_address
            ]
        ];
        return response()->json($response, 200);
    }

}
