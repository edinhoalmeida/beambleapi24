<?php
namespace Shipping;

use Shipping\Drivers\UberClient;
use Shipping\Exceptions\NotImplemented as ShippingNotImplemented;

use App\Models\Shipping\ShippingModel;

class Shipping
{
    private $shipping_driver = 'uber';

    public function create_shipping_estimate(int $beamer_id, int $client_id)
    {   
        if($this->shipping_driver!='uber'){
            throw new ShippingNotImplemented(self::class);
            exit();
        }
        
        // erase olders
        ShippingModel::where('user_id', $client_id)->where('beamer_id', $beamer_id)->delete();

        $shipping_estimate = UberClient::shipping_details($beamer_id, $client_id);

        $shipping_estimate_db = ShippingModel::create(
            [
                'user_id'=>$beamer_id,
                'beamer_id'=> $beamer_id,
                'service'=>$this->shipping_driver,
                'uber_id'=>$shipping_estimate['id'],
                'uber_fee'=>$shipping_estimate['fee'],
                'object_json'=>json_encode($shipping_estimate),
            ]
        );

        $return_data = [
            'success'   => true, 
            'shipping_estimate_id' => $shipping_estimate_db->id,
            'fee' => $shipping_estimate['fee'],
        ];
        return $return_data;
    }

    public function webHook($dado)
    {
        dblog('webhook UBER', json_encode($dado));
        $uber_object = json_encode($dado);
       /*
       */
        // if(!empty($stripe_object->type)){
        //     if($stripe_object->type=='payment_intent.succeeded'){
        //         return $this->processPaymentIntent($stripe_object->data->object);
        //     }
        // }
    }

}
