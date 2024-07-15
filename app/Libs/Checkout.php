<?php 
namespace App\Libs;

use Shipping;

class Checkout {

    private $checkout; 

    public function __construct($products, $call_id, $beamer_id, $userid, $call_cost=0, $use_wallet_amount=0)
    {
        $products_cost = 0;
        $currency = null;
        foreach ($products as $product) {
            $user_product = $product->product()->first();
            $products_cost += (int) $user_product->product_price;
            $currency = $user_product->product_currency;
        }
        $shipping = $this->shipping_set($beamer_id, $userid, $products_cost);
        $total_cost = $products_cost + $call_cost + $shipping['shipping_cost'] - $use_wallet_amount;
        $checkout = [
            'call_cost'     =>  $call_cost,
            'products_cost' =>  $products_cost,
            'shipping_cost' =>  $shipping['shipping_cost'],
            'use_wallet_amount' => $use_wallet_amount,
            'total_cost'    =>  $total_cost,
            'currency'    =>  $currency,
        ];
        $this->checkout = [
            'shipping'=>$shipping,
            'checkout'=>$checkout,
        ];
    }

    public function get(){
        return $this->checkout;
    }

    public function shipping_set__($products_cost){
        return [
            'shipping_cost'=>round(0.1 * $products_cost),
            'shipping_service'=>'Ten percent test service',
        ];
    }

    public function shipping_set($beamer_id, $userid, $products_cost){
        
        $shipping = Shipping::create_shipping_estimate($beamer_id, $userid);

        return [
            'shipping_cost'=>$shipping['fee'],
            'shipping_service'=>'Uber',
        ];
    }

}