<?php
    
namespace App\Http\Controllers;
     
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Session;
use Stripe;
     
class StripePaymentController extends BaseController
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('stripe');
    }
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
        $customer = Stripe\Customer::create(array(
            "address" => [
                    "line1" => "Costumer 2024 test, Virani Chowk",
                    "postal_code" => "360001",
                    "city" => "Rajkot",
                    "state" => "GJ",
                    "country" => "IN",
                ],
            "email" => "demo222@gmail.com",
            "name" => "Costumer 2024 test",
            "source" => $request->stripeToken
         ));

        Stripe\Charge::create ([
                "amount" => 100,
                "currency" => "eur",
                "customer" => $customer->id,
                "description" => "Teste de dados mínimos.",
                "shipping" => [
                  "name" => "Jenny Rosen",
                  "address" => [
                    "line1" => "510 Townsend St",
                    "postal_code" => "98140",
                    "city" => "San Francisco",
                    "state" => "CA",
                    "country" => "US",
                  ],
                ]
        ]); 
        Session::flash('success', 'Payment successful!');
        return back();
    }
}