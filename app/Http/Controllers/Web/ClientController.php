<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Stripe;
use Session;

use App\Models\Param;
use App\Models\User;
use App\Models\UserAddresses;
use App\Models\UserStripe;
use App\Models\UserStripeObject;
use App\Models\Inbox\Inbox;


class ClientController extends BaseController
{

    function __construct()
    {
        // $this->middleware('user_type:isClient');
    }

    public function dashboard(){
        $dados = [
            'User type' => 'client'
        ];
        return view('web.dashboard', $dados);
    }


    public function inbox($beamer_id){
        $client_id = 19;
        $client = User::find($client_id);
        $dados = [
            'client' => $client,
            'beamer' => User::find($beamer_id)
        ];
        return view('web.inbox', $dados);
    }

    public function charge()
    {
        return view('web.charge', [
            'title'=>'Payment test', 
            'form_target'=>route('web.charge.post')
        ]);
    }

    public function stripePost(Request $request)
    {

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this_user = auth()->user();
        $logged_user_id = $this_user->id;

        $user_stripe = UserStripe::where('user_id', $logged_user_id)->first();

        // card_amount
        // number_exp_year
        // number_exp_month
        // number_cvc
        // number_card
        // name_on_card
        $dados_address = [];

        if(empty($user_stripe->customer_id)){

            $dados = [];
            $dados['name'] = $request->name_on_card;
            $dados['email'] = $this_user->email;
            $dados['source'] = $request->stripeToken;
            $dados['address'] = UserAddresses::getAddressToBill($logged_user_id);
            $dados_address = $dados['address'];

            $dados['metadata'] = ['beamble_id' => $logged_user_id];

            // $sourceId = Stripe\Customer::retrieveSource($customer_id, $sourceId, $params = null, $opts = null);

            $customer = Stripe\Customer::create($dados);

            $to_user_stripe = [
                'user_id'=>$logged_user_id,
                'customer_id'=>$customer->id
            ];
            UserStripe::create($to_user_stripe);
            // $customer = Stripe\Customer::create(array(
            //     "address" => [
            //         "line1" => "Virani Chowk",
            //         "postal_code" => "360001",
            //         "city" => "Rajkot",
            //         "state" => "GJ",
            //         "country" => "IN",
            //     ],
            //     "email" => "demo222@gmail.com",
            //     "name" => "Hardik Savani",
            //     "source" => $request->stripeToken
            // ));
            $user_stripe_customer_id = $customer->id;
        } else {
            $dados = [
                'source' => $request->stripeToken
            ];
            $customer = Stripe\Customer::retrieve($user_stripe->customer_id);
            // dd($customer);

            if ( empty($customer->shipping->address) ) {
                $dados_address = UserAddresses::getAddressToBill($logged_user_id);
            } else {

                if(!empty($customer->shipping->address->city)){
                    $dados_address['city'] = $customer->shipping->address->city; 
                }
                if(!empty($customer->shipping->address->country)){
                    $dados_address['country'] = $customer->shipping->address->country; 
                }
                if(!empty($customer->shipping->address->line1)){
                    $dados_address['line1'] = $customer->shipping->address->line1; 
                }
                if(!empty($customer->shipping->address->line2)){
                    $dados_address['line2'] = $customer->shipping->address->line2; 
                }
                if(!empty($customer->shipping->address->postal_code)){
                    $dados_address['postal_code'] = $customer->shipping->address->postal_code; 
                }
                if(!empty($customer->shipping->address->state)){
                    $dados_address['state'] = $customer->shipping->address->state; 
                }

            }
            $customer = Stripe\Customer::update($user_stripe->customer_id, $dados);
            $user_stripe_customer_id = $user_stripe->customer_id;
        }

        $charge_return = Stripe\Charge::create([
            "amount" => string_to_number(
                $request->card_amount, 
                true /* true para stripe = valor com centavos mas sem vírgula */
            ),
            "currency" => "eur",
            "customer" => $user_stripe_customer_id, // cus_NFNJG5UehIgH61
            "description" => "Chargé dans l'application Beamble",
            "shipping" => [
              "name" => "Billing address",
              // Serviços não precisam de endereço de compra
              "address" => $dados_address 
              // [
              //   "line1" => "510 Townsend St",
              //   "postal_code" => "98140",
              //   "city" => "San Francisco",
              //   "state" => "CA",
              //   "country" => "US",
              // ],
            ]
        ]);

        // TODO: gravar o log do retorno
        UserStripeObject::create(
            [
                'user_id'=>$logged_user_id,
                'customer_id'=>$user_stripe_customer_id,
                'object_type'=>'charge',
                'object_json'=>json_encode($charge_return),
            ]
        );
        Session::flash('success', 'Payment successful!');
        return back();
    }

    public function connect()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripe = new \Stripe\StripeClient(
             'sk_test_51M0Wa2K6bwyRYOMDnBX4tzkBCSi8TgzfmJh37rlmf0slvG5wnCJLd2juYYWg1zHYL1v55O0cK7t9tT1AZYPA71bm00FYHcIRmb'
            );
        $stripe->accounts->all(['limit' => 3]);
        return view('web.charge');
    }

    public function connectPost(Request $request)
    {
        return view('web.charge');
    }

    public function subscription()
    {
        return view('web.charge', [
            'title'=>'Subscription test', 
            'form_target'=>route('web.subscription.post')
        ]);
    }

    public function subscriptionPost(Request $request)
    {

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this_user = auth()->user();
        $logged_user_id = $this_user->id;

        $user_stripe = UserStripe::where('user_id', $logged_user_id)->first();

        // card_amount
        // number_exp_year
        // number_exp_month
        // number_cvc
        // number_card
        // name_on_card
        $dados_address = [];

        if(empty($user_stripe->customer_id)){

            $dados = [];
            $dados['name'] = $request->name_on_card;
            $dados['email'] = $this_user->email;
            $dados['source'] = $request->stripeToken;
            $dados['address'] = UserAddresses::getAddressToBill($logged_user_id);
            $dados_address = $dados['address'];

            $dados['metadata'] = ['beamble_id' => $logged_user_id];

            // $sourceId = Stripe\Customer::retrieveSource($customer_id, $sourceId, $params = null, $opts = null);

            $customer = Stripe\Customer::create($dados);

            $to_user_stripe = [
                'user_id'=>$logged_user_id,
                'customer_id'=>$customer->id
            ];
            UserStripe::create($to_user_stripe);
            // $customer = Stripe\Customer::create(array(
            //     "address" => [
            //         "line1" => "Virani Chowk",
            //         "postal_code" => "360001",
            //         "city" => "Rajkot",
            //         "state" => "GJ",
            //         "country" => "IN",
            //     ],
            //     "email" => "demo222@gmail.com",
            //     "name" => "Hardik Savani",
            //     "source" => $request->stripeToken
            // ));
            $user_stripe_customer_id = $customer->id;
        } else {
            $dados = [
                'source' => $request->stripeToken
            ];
            $customer = Stripe\Customer::retrieve($user_stripe->customer_id);
            // dd($customer);

            if ( empty($customer->shipping->address) ) {
                $dados_address = UserAddresses::getAddressToBill($logged_user_id);
            } else {

                if(!empty($customer->shipping->address->city)){
                    $dados_address['city'] = $customer->shipping->address->city; 
                }
                if(!empty($customer->shipping->address->country)){
                    $dados_address['country'] = $customer->shipping->address->country; 
                }
                if(!empty($customer->shipping->address->line1)){
                    $dados_address['line1'] = $customer->shipping->address->line1; 
                }
                if(!empty($customer->shipping->address->line2)){
                    $dados_address['line2'] = $customer->shipping->address->line2; 
                }
                if(!empty($customer->shipping->address->postal_code)){
                    $dados_address['postal_code'] = $customer->shipping->address->postal_code; 
                }
                if(!empty($customer->shipping->address->state)){
                    $dados_address['state'] = $customer->shipping->address->state; 
                }

            }
            $customer = Stripe\Customer::update($user_stripe->customer_id, $dados);
            $user_stripe_customer_id = $user_stripe->customer_id;
        }
        
        // $account_object = \Stripe\Account::create([
        //   'type' => 'custom',
        //   'country' => $dados_address['country'],
        //   'email' => $this_user->email,
        //   'capabilities' => [
        //     'card_payments' => ['requested' => true],
        //     'transfers' => ['requested' => true],
        //   ],
        // ]);

        // UserStripeObject::create(
        //     [
        //         'user_id'=>$logged_user_id,
        //         'customer_id'=>$user_stripe_customer_id,
        //         'object_type'=>'account',
        //         'object_json'=>json_encode($account_object),
        //     ]
        // );

        $subscription_object = \Stripe\Subscription::create(
          [
            'customer' => $user_stripe_customer_id,
            'items' => [['price' => 'price_1MW3YyK6bwyRYOMDCYzOXnBR']],
            'expand' => ['latest_invoice.payment_intent'],
          ],
          // ['stripe_account' => $account_object->id]
        );

        UserStripeObject::create(
            [
                'user_id'=>$logged_user_id,
                'customer_id'=>$user_stripe_customer_id,
                'object_type'=>'subscription',
                'object_json'=>json_encode($subscription_object),
            ]
        );


        // tabela grava retorno do stripe:
        /*
        user_stripe_object
        id AIncrement
        user_id
        customer_id
        object_type
        object_json (json_encode)


            // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = new \Stripe\StripeClient('sk_test_51M0Wa2K6bwyRYOMDnBX4tzkBCSi8TgzfmJh37rlmf0slvG5wnCJLd2juYYWg1zHYL1v55O0cK7t9tT1AZYPA71bm00FYHcIRmb');

        $stripe->subscriptions->create(
          [
            'customer' => '{{CUSTOMER}}',
            'items' => [['price' => '{{PRICE}}']],
            'expand' => ['latest_invoice.payment_intent'],
          ],
          ['stripe_account' => '{{CONNECTED_ACCOUNT_ID}}']
        );

        
        */

        Session::flash('success', 'Subscription successful!');
        return back();

    }
}