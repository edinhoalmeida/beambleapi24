<?php
namespace Payment\Drivers;

define('STRIPE_API_KEY', getenv('STRIPE_KEY'));
define('STRIPE_SECRET', getenv('STRIPE_SECRET'));
define('STRIPE_API_VERSION', getenv('STRIPE_API_VERSION'));

use Payment\Contract\Contract as PaymentContract;
use Payment\Exceptions\NotImplemented as PaymentNotImplemented;

use Payment\PaymentRessource;

use Stripe;

class StripeClient implements PaymentContract {

    public static $client = null;

    public static function getClient($type = 'public'){

        if( ! is_null(self::$client) ){
            return self::$client;
        }
        \Stripe\Stripe::setAppInfo(
            "BeambleFrance",
            "0.0.1",
            "https://www.beamble.com"
        );
        if($type=='public'){
            self::$client = new \Stripe\StripeClient(STRIPE_API_KEY);
        } else {
            self::$client = new \Stripe\StripeClient(STRIPE_SECRET);            
        }
        return self::$client;
    }

	public static function accountStatus($account_id)
	{
        $client = self::getClient('secret');
		$stripe_account = $client->accounts->retrieve($account_id);
		if (empty($stripe_account->payouts_enabled)) {
			return false;
		}
		if (empty($stripe_account->capabilities->transfers) || $stripe_account->capabilities->transfers == 'inactive') {
			return false;
		}
		return true;
	}

    public static function accountCreateEmpty()
    {
        $client = self::getClient('secret');
        $stripe_account = $client->accounts->create(['type' => 'standard']);
        return $stripe_account;
    }
    public static function accountLinkCreate($stripe_account_id, $refresh_url, $return_url)
    {
        $client = self::getClient('secret');
        $account_link = $client->accountLinks->create([
            'account' => $stripe_account_id,
            'refresh_url' => $refresh_url,
            'return_url' =>  $return_url,
            'type' => 'account_onboarding',
        ]);
		return $account_link;
	}

    public static function paymentReadyOk(int $user_id){
        $client = self::getClient();
        return true;
    }

    public static function costumerCreateEmpty($description = 'new client')
    {
        $client = self::getClient('secret');
		$customer = $client->customers->create(['description' => $description]);
		return $customer;
	}

    public static function costumerPaymentMethod($custumer_test){
        $client = self::getClient('secret');
        $ret = ['success'=>false];
        try {
            $paymentMethods = $client->customers->allPaymentMethods($custumer_test);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            dblog('payment_api_error', $e->getError()->message);
            return $ret;
        } catch (Exception $e) {
            dblog('payment_general_error', $e->getError()->message);
            return $ret;
        }
        if(empty($paymentMethods->data[0]->id)){
            dblog('payment_paymentMethods', json_encode($paymentMethods->id));
            return $ret;
        }
        $source_pay_id = $paymentMethods->data[0]->id;
        $ret = ['success'=>true, 'payment_source'=>$source_pay_id];
        return $ret;
    }

    public static function tryCharge($amount, $currency, $costumer, $custumer_name, $source_pay_id, $fee, $transfer_account, $metadata = ['mode'=>'payment_intent'])
    {
        $client = self::getClient('secret');
        $ret = ['success'=>false, 'msg'=>'failure', 'json_return'=>null];
        try {
            $parameters = [
                "amount" => $amount,
                "currency" => $currency,
                "customer" => $costumer, 
                "confirm" => true,
                'off_session' => true,
                "description" => "Beamble - service de téléportation",
                'metadata' => $metadata,
                "payment_method" => $source_pay_id,
                'application_fee_amount' => $fee,
                'transfer_data[destination]' => $transfer_account,
                "shipping" => [
                  "name" => $custumer_name . " adresse",
                  // Serviços não precisam de endereço de compra
                  "address" => 
                  [
                    "line1" => "Ne s'applique pas aux services en ligne",
                  //   "postal_code" => "04003005",
                  //   "city" => "Sao paulo",
                  //   "state" => "SP",
                  //   "country" => "BR",
                  ],
                ]
            ];
            $opts = ['stripe_version' => STRIPE_API_VERSION];
            $charge_return = $client->paymentIntents->create($parameters, $opts);
    
        } catch (Exception $e) {    
            $erro = $e->getError()->code . ' = ' . $e->getError()->message;
            $ret['msg'] = $erro;
            return $ret;
        }

        // Status of this PaymentIntent, 
        // one of requires_payment_method, requires_confirmation, requires_action, 
        // processing, requires_capture, canceled, or succeeded

        $ret['json_return'] = $charge_return;
        if($charge_return->status=='succeeded'){
            $ret['success'] = true;
            $ret['msg'] = 'Paiement effectué avec carte enregistrée';
        } elseif($charge_return->status=='requires_confirmation'){
            $ret['msg'] = "Transaction en attente avec carte enregistrée";
        } elseif($charge_return->status=='canceled'){
            $ret['msg'] = "Transaction non autorisée avec carte enregistrée";
        } else {
            // pending
            $ret['msg'] = "La transaction ne correspond pas à la carte enregistrée";
        }
        return $ret;
    }
    
    public static function ephemeralKeysCreate($customer_id)
    {
        $client = self::getClient('secret');
        $opts = ['stripe_version' => STRIPE_API_VERSION];
		$stripe_ephemeral = $client->ephemeralKeys->create([
            'customer' => $customer_id,
        ], $opts);
		return $stripe_ephemeral;
	}

    public static function paymentIntentCreate($dado)
    {
        unset($dado['payment_intent_id']);
        $client = self::getClient('secret');
        if(empty($dado['application_fee_amount']) || empty($dado['transfer_data[destination]'])){
            unset($dado['application_fee_amount']);
            unset($dado['transfer_data[destination]']);
        }
        $opts = ['stripe_version' => STRIPE_API_VERSION];
		$payment_intent = $client->paymentIntents->create($dado, $opts);
		return $payment_intent;
	}

    public static function paymentIntentGet($pi_id)
    {
        $client = self::getClient('secret');
        $opts = ['stripe_version' => STRIPE_API_VERSION];
		$payment_intent = $client->paymentIntents->retrieve($pi_id, [], $opts);
		return $payment_intent;
	}

}