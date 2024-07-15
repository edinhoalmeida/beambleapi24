<?php 
namespace App\Libs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/*
FR

postman format


address_components[0][long_name]:25
address_components[0][types][0]:street_number
address_components[1][long_name]:Avenue des Champs-Élysées
address_components[1][types][0]:route
address_components[2][long_name]:Paris
address_components[2][types][0]:locality
address_components[3][long_name]:Département de Paris
address_components[3][types][0]:administrative_area_level_2
address_components[4][long_name]:Île-de-France
address_components[4][types][0]:administrative_area_level_1
address_components[5][long_name]:France
address_components[5][short_name]:FR
address_components[5][types][0]:country
address_components[6][long_name]:75008
address_components[6][types][0]:postal_code


*/

class WorldAddress {

    protected $uri              = "https://maps.googleapis.com/maps/api/geocode/json?";
    protected $uri_timezone     = "https://maps.googleapis.com/maps/api/timezone/json?";
    protected $key              = '';

    public $requestURL = null;
    public $response   = null;
    public $address   = null;

    public function __construct($address){
      $this->address = $address;
    }

    public static function getAddressFmt($address_components=[]){

        if(isset($address_components['address_components'])){
            $address_components = $address_components['address_components'];
        } 
        // dd($address_components);
        // $ac = Arr::get($response, 'results.0.address_components', []);

        // predefine final variable
        $address = [
          'street'        => '',
          'street2'       => '',
          'street_number' => '',
          'address'       => '',
          'city'          => '',
          'postal_code'   => '',
          'others'        => '',
          'others_key'    => '',
          'country'       => '',
          'country_code'  => '',
        ];

        // find country code
        $country_code = 'general';
        foreach($address_components as $i=>$item):
            $type  = Arr::get($item, 'types.0');
            if($type=='country'){
                $address['country'] = Arr::get($item, 'long_name');
                $country_code = $address['country_code'] = Arr::get($item, 'short_name');
                unset($address_components[$i]);
                break;
            }
        endforeach;

        // loop though others address_components
        foreach($address_components as $i=>$item):
            $value = Arr::get($item, 'long_name');
            $type  = Arr::get($item, 'types.0');
            
            switch ($type):
                case 'street_number':
                  $address['street_number'] = $value;
                  unset($address_components[$i]);
                break;

                case 'route':
                  $address['street']  = $value;
                  unset($address_components[$i]);
                break;

                case 'locality':
                  $address['city'] = $value;
                  unset($address_components[$i]);
                break;

                case 'postal_code':
                  $address['postal_code'] = $value . $address['postal_code'];
                  unset($address_components[$i]);
                break;

                case 'postal_code_suffix':
                  $address['postal_code'] = $address['postal_code'] .'-'. $value;
                  unset($address_components[$i]);
                break;


                // UK and in Sweden, the component to display the city is postal_town.
                // Sweden = SE UK = "SESE"
                case 'postal_town':
                    if(in_array($country_code, ['SE','UK'])){
                        $address['city'] = $value;
                        unset($address_components[$i]);
                    }
                break;
                // Brooklyn and other parts of New York City do not include the city as part of the address. Instead, they use sublocality_level_1
                case 'sublocality_level_1':
                    if(in_array($country_code, ['US'])){
                        $address['city'] = $value;
                        unset($address_components[$i]);
                    }
                break;

                // citi in brazil BR
                case 'administrative_area_level_2':
                    if(in_array($country_code, ['BR'])){
                        $address['city'] = $value;
                        unset($address_components[$i]);
                    }
                break;

            endswitch;
        endforeach;
        $demais_campos = [];
        foreach($address_components as $i=>$item):
            $value = Arr::get($item, 'long_name');
            $type  = Arr::get($item, 'types.0');
            $demais_campos[$type] = $value;
        endforeach;
        if(count($demais_campos)>1){
            ksort($demais_campos);
        }
        if(!empty($demais_campos)){
            $address['others'] = implode(", ",array_values($demais_campos));
            $address['others_key'] = implode(", ",array_keys($demais_campos));
        }

        return $address;
    }

    public function request($data=[]){
        $conf_maps = config('maps');
        $params   = array_merge(['key' => $conf_maps['key']], $data);
        $response = Http::get( $this->uri, $params);
        $response = json_decode($response->body(), true);
        return  $response;
    }

    public function request_tz($data=[]){
      $conf_maps = config('maps');
      $params   = array_merge(['key' => $conf_maps['key']], $data);
      $response = Http::get( $this->uri_timezone, $params);
      $response = json_decode($response->body(), true);
      return  $response;
    }

    public function request_lat_lng(){
      $response = $this->request(['address'=>$this->address]);
      $ac = Arr::get($response, 'results.0.geometry.location', []);
      if(!empty($ac['lat'])){
        $response2 = $this->request_tz(
          [
            'location'=>implode(',', $ac),
            'timestamp'=>time()
          ]);
        if(!empty($response2['status']) && $response2['status']=='OK'){
          $ac['raw_off_set'] = $response2['rawOffset'];
        } else {
          $ac['raw_off_set'] = 0;
        }
        $dados = self::getAddressFmt(Arr::get($response, 'results.0', []));
        $ac['country_code'] = $dados['country_code'];
      }
      return  $ac;
    }

    public function getAddress($response=[]){

        $ac = Arr::get($response, 'results.0.address_components', []);

        // predefine final variable
        $address = [
          'street'        => '',
          'nr'            => '',
          'address'       => '',
          'city'          => '',
          'postal_code'   => '',
          'country'       => '',
          'formatted'     => Arr::get($response, 'results.0.formatted_address'),
        ];

        // loop though address_components
        foreach($ac as $item):
            $value = Arr::get($item, 'long_name');
            $type  = Arr::get($item, 'types')[0];

            switch ($type):
                case 'street_number':
                  $address['nr']      = $value;
                  $address['address'] = strlen($address['address']) == 0 ? ($address['address'] . ' ' . $value) : ($value . $address['address']);
                break;

                case 'route':
                  $address['street']  = $value;
                  $address['address'] = strlen($address['address']) == 0 ? ($address['address'] . ' ' . $value) : ($value . $address['address']);
                break;

                case 'locality':
                  $address['city'] = $value;
                break;

                case 'postal_code':
                  $address['postal_code'] = $value;
                break;

                case 'country':
                  $address['country'] = $value;
                break;

            endswitch;
        endforeach;

        return $address;
    }

    public function getLocation($response=[]){
        return Arr::get($response, 'results.0.geometry.location');
    }


    public function getStatus($response=[]){
        return Arr::get($response, 'status', 'STATUS_NOT_FOUND');
    }


    public function getGeoLocation(Request $r){
        $params = [];

        if($r->input('q'))
          $params['address'] = $r->input('q');

        if($r->input('latlng'))
          $params['latlng'] = $r->input('latlng');

        $response = $this->request($params);
        $location = $this->getLocation($response);
        $status   = $this->getStatus($response);
        $address  = $this->getAddress($response);

        return response()->json([
            // 'all'       => $r->all(), // debug only
            'response'  => $response,
            'address'   => $address,
            'location'  => $location,
            'status'    => $status,
        ]);
    }

}