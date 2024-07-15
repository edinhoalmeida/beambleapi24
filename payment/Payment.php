<?php

namespace Payment;

use Payment\Drivers\Google as GoogleDriver;
use Payment\Drivers\StripeClient as StripeClient;

use Payment\Exceptions\NotImplemented as PaymentNotImplemented;

use App\Models\User;
use App\Models\UserAddresses;
use App\Models\UserStripeAccount;
use App\Models\UserStripeCustomer;
use App\Models\UserStripeEphemeral;
use App\Models\UserStripePaymentIntent;
use App\Models\UserStripeObject;
use App\Models\Financial\Payment as FinPayment;
use Stripe\FundingInstructions;

use Illuminate\Support\Str;
class Payment
{
    private $payment_driver = 'stripe';

    // errors codes
    const STATUS_USER_NOT_FOUND = -1;
    const STATUS_USER_COSTUMER_EMPTY = -2;
    const STATUS_USER_ACCOUNT_EMPTY = -3;
    const STATUS_USER_ACCOUNT_PENDING = -4;
    const STATUS_USER_COSTUMER_PENDING = -5;

    const STATUS_CLIENT_COSTUMER_CREATED = 2;
    const STATUS_CLIENT_COSTUMER = 3;

    const STATUS_USER_AND_ACCOUNT_ENABLED = 1;

    public static $account_id_pending = null;
    public static $account_token = null;

    public function createPaymentIntent($user_id, $dado)
    {   
        $customer = Payment::customerByUserId($user_id);

        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit();
        }
        // erase olders
        UserStripePaymentIntent::where('user_id', $user_id)->delete();

        if(!empty($dado['beamer_id'])){
            //
            $connected_status = UserStripeAccount::where('user_id', $dado['beamer_id'])->first();

            if(empty($connected_status->account_stripe_enabled) || empty($dado['application_fee_amount'])){
                unset($dado['beamer_id']);
                unset($dado['application_fee_amount']);
            } else {
                unset($dado['beamer_id']);
                $dado['transfer_data[destination]'] = $connected_status->account_id;
            }
            // application
            // application_fee_amount
        }
        if(!empty($customer['customer_id'])){
            $dado['customer'] = $customer['customer_id'];
            $dado['setup_future_usage'] = 'off_session';
        }
        $dado['metadata'] = [
            'mode'=>'payment_intent',
            'client_id'=>$user_id,
            'beamer_id'=>empty($dado['beamer_id'])?0:$dado['beamer_id'],
            'call_id'=>empty($dado['call_id'])?0:$dado['call_id'],
            'is_guarantee'=>empty($dado['is_guarantee'])?0:$dado['is_guarantee']
        ];
        unset($dado['is_guarantee']);
        $payment_intent = StripeClient::paymentIntentCreate($dado);
        
        UserStripeObject::create(
            [
                'user_id'=>$user_id,
                'customer_id'=> $customer['customer_id'],
                'object_type'=>'create_payment_intent',
                'object_json'=>json_encode($payment_intent),
            ]
        );
        UserStripePaymentIntent::create(
            [
                'user_id'=>$user_id,
                'customer_id'=> $customer['customer_id'],
                'payment_intent_id'=>$payment_intent->id,
                'client_secret' => $payment_intent->client_secret,
            ]
        );
        $return_data = [
            'success'   => true, 
            'payment_intent_id' => $payment_intent->id,
            'client_secret' => $payment_intent->client_secret
        ];
        return $return_data;
    }
    public function createPayCall($user_id, $dado)
    {   
        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit();
        }
        $return_data = [
            'success'   => false, 
            'payment_id' => null
        ];
        $customer = Payment::customerByUserId($user_id);
        // erase olders
        $payment_source = StripeClient::costumerPaymentMethod($customer['customer_id']);
        if($payment_source['success']==false){
            $return_data['error']='client default payment method not setted';
            FinPayment::create([
                'user_id_client'=>$user_id,
                'user_id_beamer'=>$dado['beamer_id'],
                'call_id'=>$dado['call_id'],
                'is_guarantee'=>0,
                'type'=>'pay_call',
                'amount'=>$dado['amount'],
                'fee'=>$dado['application_fee_amount'],
                'method_type'=>$payment_source['payment_source'],
                'status'=>'error - customer not able'
            ]);
            return $return_data;
        }
        $beamer_stripe = UserStripeAccount::where('user_id', $dado['beamer_id'])->first();
        if(empty($beamer_stripe->account_stripe_enabled) || empty($beamer_stripe->account_id)){
            $return_data['error']='beamer not able to receive payments';
            FinPayment::create([
                'user_id_client'=>$user_id,
                'user_id_beamer'=>$dado['beamer_id'],
                'call_id'=>$dado['call_id'],
                'is_guarantee'=>0,
                'type'=>'pay_call',
                'amount'=>$dado['amount'],
                'fee'=>$dado['application_fee_amount'],
                'method_type'=>$payment_source['payment_source'],
                'status'=>'error - beamer not able'
            ]);
            return $return_data;
        }

        $customer_name = User::find($user_id);
        $customer_name = $customer_name->full_name;
        $metadata = [//metadata
            'mode'=>'payment_intent',
            'client_id'=>$user_id,
            'beamer_id'=>$dado['beamer_id']
        ];
        $payment = StripeClient::tryCharge(
            $dado['amount'], 
            $dado['currency'], 
            $customer['customer_id'], 
            $customer_name, 
            $payment_source['payment_source'], 
            $dado['application_fee_amount'], 
            $beamer_stripe->account_id,
            $metadata
        );

        if(!empty($payment['json_return'])){
            $obj = $payment['json_return'];
        } else {
            $obj = $payment;
        }

        UserStripeObject::create(
            [
                'user_id'=>$user_id,
                'customer_id'=> $customer['customer_id'],
                'account_id'=> $beamer_stripe->account_id,
                'object_type'=>'payment_charge',
                'object_json'=>json_encode( $obj ),
            ]
        );

        FinPayment::create([
            'user_id_client'=>$user_id,
            'user_id_beamer'=>$dado['beamer_id'],
            'call_id'=>$dado['call_id'],
            'is_guarantee'=>0,
            'type'=>'pay_call',
            'amount'=>$dado['amount'],
            'amount_products'=>$dado['amount_products'],
            'fee'=>$dado['application_fee_amount'],
            'method_type'=>$payment_source['payment_source'],
            'status'=>($payment['success']==true)?'paid':'pending'
        ]);

        $return_data = [
            'success'   => $payment['success'], 
            'payment_intent_id' => $payment['json_return']->id,
            'payment_status' => $payment['json_return']->status
        ];
        return $return_data;
    }
    public function createEphemeralKey($userid)
    {   
        $customer = Payment::customerByUserId($userid);
        
        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit();
        }
        // erase olders ephemerals
        UserStripeEphemeral::where('user_id', $userid)->delete();

        $ephemeral = StripeClient::ephemeralKeysCreate($customer['customer_id']);
        UserStripeObject::create(
            [
                'user_id'=>$userid,
                'customer_id'=>$customer['customer_id'],
                'object_type'=>'create_ephemeral_key',
                'object_json'=>json_encode($ephemeral),
            ]
        );
        UserStripeEphemeral::create(
            [
                'user_id'=>$userid,
                'customer_id'=> $customer['customer_id'],
                'ephemeral_key'=>$ephemeral->secret
            ]
        );
        $return_data = [
            'success'   => true, 
            'ephemeralKey' => $ephemeral->secret
        ];
        return $return_data;
        
    }

    public function customerByUserId($user_id)
    {
        $return_data = [
            'success'   => false, 
            'status_code'   =>  Payment::STATUS_USER_NOT_FOUND,
            'customer_id'   => null,
        ];
        $user = User::find($user_id);
        if(empty($user)){
            return  $return_data;
        }

        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit;
        }
        
        $user_stripe = UserStripeCustomer::where('user_id', $user_id)->first();
        
        if(empty($user_stripe->customer_id)){
            // is not created a account id for this user
            $description = "Client: " . $user->surname . ", " .  $user->name;
            $customer = StripeClient::costumerCreateEmpty($description);

            UserStripeObject::create(
                [
                    'user_id'=>$user_id,
                    'customer_id'=> $customer->id,
                    'object_type'=>'create_customer',
                    'object_json'=>json_encode($customer),
                ]
            );

            $uS = UserStripeCustomer::updateOrCreate(
                ['user_id' => $user_id]
            );
            $uS->customer_id = $customer->id;
            $uS->save();
            $return_data = [
                'success'   => true, 
                'status_code'   =>  Payment::STATUS_CLIENT_COSTUMER_CREATED,
                'customer_id'   =>  $customer->id,
            ];
        } else {
            // test if payment is enable
            $pay_method = StripeClient::costumerPaymentMethod($user_stripe->customer_id);
            if($pay_method['success']==true){
                $user_stripe->customer_stripe_enabled = 1;
                $user_stripe->save();
            }
            $return_data = [
                'success'   => true, 
                'status_code'   =>  Payment::STATUS_CLIENT_COSTUMER,
                'customer_id'   =>  $user_stripe->customer_id,
            ];
        }

        return  $return_data;
    }

    public function getAccountLink($user_id, $stripe_account_id, $refresh_url, $return_url)
    {
        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit;
        }

        $account_link = StripeClient::accountLinkCreate($stripe_account_id, $refresh_url, $return_url);

        UserStripeObject::create(
            [
                'user_id'=>$user_id,
                'account_id'=> $stripe_account_id,
                'object_type'=>'create_account_link onboarding',
                'object_json'=>json_encode($account_link),
            ]
        );

        if(!empty($account_link->url)){
            return $account_link->url;
        }
        return false;
    }
    public function checkAccountUserById($user_id)
    {
        $user = User::find($user_id);
        if(empty($user)){
            return Payment::STATUS_USER_NOT_FOUND;
        }

        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit;
        }
            
        $user_stripe = UserStripeAccount::where('user_id', $user_id)->first();
        
        if(empty($user_stripe->account_id)){
            // is not created a account id for this user
            $account = StripeClient::accountCreateEmpty();

            UserStripeObject::create(
                [
                    'user_id'=>$user_id,
                    'account_id'=> $account->id,
                    'object_type'=>'create_account',
                    'object_json'=>json_encode($account),
                ]
            );

            $uS = UserStripeAccount::updateOrCreate(
                ['user_id' => $user_id]
            );
            $uS->account_id = $account->id;
            $tokk = Str::random(32);
            $uS->account_token = $tokk;
            $uS->save();
            Payment::$account_id_pending = $account->id;
            Payment::$account_token = $tokk;
            return Payment::STATUS_USER_ACCOUNT_PENDING;
        }

        if(empty($user_stripe->account_stripe_enabled)){
            Payment::$account_id_pending = $user_stripe->account_id;
            Payment::$account_token = $user_stripe->account_token;
            return Payment::STATUS_USER_ACCOUNT_PENDING;
        } else {
            return Payment::STATUS_USER_AND_ACCOUNT_ENABLED;
        }
    }
    public function getUserIdByAccountToken($accounttk)
    {
        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit;
        }

        $user_stripe = UserStripeAccount::where('account_token', $accounttk)->first();
        if(empty($user_stripe->user_id)){
            return Payment::STATUS_USER_NOT_FOUND;
        }

        return $user_stripe->user_id;
    }

    public function getAccountIdByToken($accounttk)
    {
        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit;
        }
        $user_stripe = UserStripeAccount::where('account_token', $accounttk)->first();
        if(empty($user_stripe->account_id)){
            return Payment::STATUS_USER_NOT_FOUND;
        }
        return $user_stripe->account_id;
    }

    public function accountStatus($user_id, $stripe_account_id)
    {
        if($this->payment_driver!='stripe'){
            throw new PaymentNotImplemented(self::class);
            exit;
        }
        $status = StripeClient::accountStatus($stripe_account_id);
        if($status==true){
            // if is true is completed a connect
            $uS = UserStripeAccount::updateOrCreate(
                ['user_id' => $user_id]
            );
            $uS->account_id = $stripe_account_id;
            $uS->account_stripe_enabled = 1;
            $uS->account_token = NULL;
            $uS->save();
        }
        return $status;
    }

    public function paymentIntentSuccess($pi_id){
        $payment_intent = StripeClient::paymentIntentGet($pi_id);
        return $this->processPaymentIntent($payment_intent);
    }



    public function webHook($dado)
    {
        dblog('webhook dado', json_encode($dado));
        $stripe_object = json_encode($dado);
       /*
       {
  "id": "evt_3NGOJLK6bwyRYOMD14ESfrt2",
  "object": "event",
  "api_version": "2022-11-15",
  "created": 1686151813,
  "data": {
    "object": {
      "id": "pi_3NGOJLK6bwyRYOMD1Yhln2Dl",
      "object": "payment_intent",
      ......
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_SlFE5dQF76Pdw6",
    "idempotency_key": "e847dbaa-5da8-477e-8c86-0f91b87ead3d"
  },
  "type": "payment_intent.succeeded"
}
       */
        if(!empty($stripe_object->type)){
            if($stripe_object->type=='payment_intent.succeeded'){
                return $this->processPaymentIntent($stripe_object->data->object);
            }
        }
    }

    public function processPaymentIntent($stripe_object)
    {
       /*
       {
      "id": "pi_3NGOJLK6bwyRYOMD1Yhln2Dl",
      "object": "payment_intent",
      "amount": 742,
      "amount_capturable": 0,
      "amount_details": {
        "tip": {
        }
      },
      "amount_received": 742,
      "application": null,
      "application_fee_amount": 1,
      "automatic_payment_methods": null,
      "canceled_at": null,
      "cancellation_reason": null,
      "capture_method": "automatic",
      "client_secret": "pi_3NGOJLK6bwyRYOMD1Yhln2Dl_secret_NbuQCqeU45PVrnKCP4TD8DtKc",
      "confirmation_method": "automatic",
      "created": 1686151811,
      "currency": "eur",
      "customer": "cus_NFWSfxAod510vU",
      "description": "Beamble - service de téléportation",
      "invoice": null,
      "last_payment_error": null,
      "latest_charge": "ch_3NGOJLK6bwyRYOMD1XdWgxbc",
      "livemode": false,
      "metadata": {
        "mode": "payment_intent"
      },
      "next_action": null,
      "on_behalf_of": null,
      "payment_method": "pm_1NDu6vK6bwyRYOMDHPFltpBr",
      "payment_method_options": {
        "card": {
          "installments": null,
          "mandate_options": null,
          "network": null,
          "request_three_d_secure": "automatic"
        }
      },
      "payment_method_types": [
        "card"
      ],
      "processing": null,
      "receipt_email": null,
      "review": null,
      "setup_future_usage": null,
      "shipping": {
        "address": {
          "city": null,
          "country": null,
          "line1": "Ne s'applique pas aux services en ligne",
          "line2": null,
          "postal_code": null,
          "state": null
        },
        "carrier": null,
        "name": "sobrenome, edinho editado adresse",
        "phone": null,
        "tracking_number": null
      },
      "source": null,
      "statement_descriptor": null,
      "statement_descriptor_suffix": null,
      "status": "succeeded",
      "transfer_data": {
        "destination": "acct_1NBJqs2fui6XT6zg"
      },
      "transfer_group": "group_pi_3NGOJLK6bwyRYOMD1Yhln2Dl"
        */
        $call_id = $client_id = $beamer_id = 0;
        if(!empty($stripe_object->metadata)){
            $call_id = empty($stripe_object->metadata->call_id) ? 0 : $stripe_object->metadata->call_id;
            $client_id = empty($stripe_object->metadata->client_id) ? 0 : $stripe_object->metadata->client_id;
            $beamer_id = empty($stripe_object->metadata->beamer_id) ? 0 : $stripe_object->metadata->beamer_id;
        }
        $pi_status = $stripe_object->status;

        FinPayment::create([
            'user_id_client'=>$client_id,
            'user_id_beamer'=>$beamer_id,
            'call_id'=>$call_id,
            'is_guarantee'=>0,
            'type'=>'pay_call',
            'amount'=>$stripe_object->amount,
            'fee'=>$stripe_object->application_fee_amount,
            'method_type'=>$stripe_object->payment_method,
            'status'=>$pi_status
        ]);

        if($pi_status=='succeeded' && !empty($client_id)){
            $user  = User::find($client_id);
            $user->customer->customer_id = $stripe_object->customer;
            $user->customer->customer_stripe_enabled = 1;
            $user->customer->save();
        }
        return true;
    }

}
