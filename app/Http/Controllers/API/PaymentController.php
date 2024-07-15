<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Validator;
use DB;

use App\Models\Param;

use App\Models\VideocallProducts;
use App\Models\Videocall;
use App\Libs\Checkout;

use Payment;

class PaymentController extends BaseController
{
/**
  * @OA\Get(
  *     path="/payments/customer",
  *     description="Get (or create) a customer_id on Stripe",
  *     tags={"payments 🔒"},
  *     @OA\Response(
  *          response="200",
  *          description="Returns a customer_id",
  *          @OA\JsonContent(
  *                @OA\Property(
  *                      property="success",
  *                      type="boolean",
  *                  ),
  *                  @OA\Property(
  *                      property="status_code",
  *                      type="integer",
  *                      example="3",
  *                  ),
  *                  @OA\Property(
  *                      property="customer_id",
  *                      type="string",
  *                      example="cus_9999",
  *                      description="A stripe customer id"
  *                  ),
  *                  @OA\Property(
  *                      property="messages",
  *                      type="array",
  *                      @OA\Items(),
  *                  ),
  *          ),
  *     ),
  *     @OA\Response(
  *          response="400",
  *          description="Auth error occurred",
  *          @OA\JsonContent(
  *            ref="#/components/schemas/baseerror",
  *          ),
  *     ),
  * )
  */
    public function getCustomer(Request $request)
    {
        $userid = auth()->user()->id;
        $customer = Payment::customerByUserId($userid);
        $customer['messages'] = [];
        return response()->json($customer, 200);
    }

/**
  * @OA\Get(
  *     path="/payments/ephemeralkey",
  *     description="Create and return ephemeralkey on Stripe",
  *     tags={"payments 🔒"},
  *     @OA\Response(
  *          response="200",
  *          description="Returns ephemeralkey",
  *          @OA\JsonContent(
  *               @OA\Property(
  *                      property="success",
  *                      type="boolean",
  *               ),
  *               @OA\Property(
  *                      property="ephemeralKey",
  *                      type="string",
  *                      example="ek_test_YWNjdF8xTT....3245345",
  *                      description="A stripe Ephemeral Key"
  *              ),
  *              @OA\Property(
  *                      property="messages",
  *                      type="array",
  *                      @OA\Items(),
  *              ),
  *          ),
  *     ),
  *     @OA\Response(
  *          response="400",
  *          description="Auth error occurred",
  *          @OA\JsonContent(
  *            ref="#/components/schemas/baseerror",
  *          ),
  *     ),
  * )
  */
    public function createEphemeralKey(Request $request)
    {
        $userid = auth()->user()->id;
        dblog('createEphemeralKey', 'user_id: ' . $userid);
        $ephemeral_key = Payment::createEphemeralKey($userid);
        $ephemeral_key['messages'] = [];
        return response()->json($ephemeral_key, 200);
    }

/**
  * @OA\Post(
  *     path="/payments/success",
  *     tags={"payments 🔒"},
  *     description="Reports the success of a payment transaction with Stripe",
  *     @OA\RequestBody(
  *          @OA\MediaType(
  *              mediaType="multipart/form-data",
  *              @OA\Schema(
  *                  @OA\Property(
  *                      property="payment_intent_id",
  *                      type="string",
  *                      example="pi_3N8k0RK6bwy......DLer",
  *                  ),
  *              )
  *          )
  *     ),
  *     @OA\Response(
  *          response="200",
  *          description="Success return of request",
  *          @OA\JsonContent(
  *               @OA\Property(
  *                      property="success",
  *                      type="boolean",
  *                      example="true",
  *               ),
  *              @OA\Property(
  *                      property="messages",
  *                      type="array",
  *                      @OA\Items(),
  *              ),
  *          ),
  *     ),
  *     @OA\Response(
  *          response="400",
  *          description="Auth error",
  *          @OA\JsonContent(
  *            ref="#/components/schemas/baseerror",
  *          ),
  *     ),
  * )
  */
    public function paymentIntentSuccess(Request $request)
    {
        $userid = auth()->user()->id;
        $dado = $request->all();
        $validator = Validator::make($dado, [
            'payment_intent_id' => 'required'
        ]);
        if($validator->fails()) {
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }
        $return = ['success' =>  Payment::paymentIntentSuccess($dado['payment_intent_id']) ];
        $return['messages'] = [];
        return response()->json($return, 200);
    }

/**
  * @OA\Get(
  *     path="/payments/paymentintent/guarantee",
  *     description="Create a payment intent for a 1 euro deposit (no need to send parameters)",
  *     tags={"payments 🔒"},
  *     @OA\Response(
  *          response="200",
  *          description="Returns payment intent id",
  *          @OA\JsonContent(
  *               @OA\Property(
  *                      property="success",
  *                      type="boolean",
  *               ),
  *               @OA\Property(
  *                      property="payment_intent_id",
  *                      type="string",
  *                      example="pi_3Nbr...MD0nXVeXaX",
  *                      description="A stripe payment intent id"
  *              ),
  *               @OA\Property(
  *                      property="client_secret",
  *                      type="string",
  *                      example="pi_3NG4brK..._secret_...hUfR2",
  *                      description="A stripe client secret"
  *              ),
  *              @OA\Property(
  *                      property="amount",
  *                      type="integer",
  *                      example="100"
  *              ),
  *              @OA\Property(
  *                      property="currency",
  *                      type="string",
  *                      example="eur"
  *              ),
  *              @OA\Property(
  *                      property="payment_method_types",
  *                      type="array",
  *                      @OA\Items(),
  *                      example={"card"}
  *              ),
  *              @OA\Property(
  *                      property="messages",
  *                      type="array",
  *                      @OA\Items(),
  *              ),
  *          ),
  *     ),
  *     @OA\Response(
  *          response="400",
  *          description="Auth error occurred",
  *          @OA\JsonContent(
  *            ref="#/components/schemas/baseerror",
  *          ),
  *     ),
  * )
  */
    public function createPaymentGuarantee(Request $request)
    {
        $userid = auth()->user()->id;
        $dado = [
            'amount' => 100,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'is_guarantee' => 1,
        ];
        // ['amount' => 1099,
        // 'currency' => 'usd', 'payment_method_types' => ['card']]
        // ['beamer_id' => ] <-- the payment needs this to do splits of values
        $payment_intent = Payment::createPaymentIntent($userid, $dado);

        $payment_intent += $dado;
        // pi_3N6a55K6bwyRYOMD00lwM9CL
        $payment_intent['messages'] = [];
        return response()->json($payment_intent, 200);
    }

/**
  * @OA\Post(
  *     path="/payments/paymentintent",
  *     tags={"payments 🔒"},
  *     description="At the end of call, this route create a payment intent to UI of Stripe+ReactJS",
  *     @OA\RequestBody(
  *          @OA\MediaType(
  *              mediaType="multipart/form-data",
  *              @OA\Schema(
  *                  @OA\Property(
  *                      property="call_id",
  *                      type="integer",
  *                  ),
  *                  @OA\Property(
  *                      property="amount",
  *                      type="integer",
  *                      description="call amount in cents",
  *                  ),
  *                  @OA\Property(
  *                      property="currency",
  *                      type="string",
  *                      example="eur",
  *                  ),
  *                  @OA\Property(
  *                     property="payment_method_types[0]",
  *                     type="string",
  *                     example="card",
  *                 ),
  *              )
  *          )
  *     ),
  *     @OA\Response(
  *          response="200",
  *          description="Returns payment intent id",
  *          @OA\JsonContent(
  *               @OA\Property(
  *                      property="success",
  *                      type="boolean",
  *               ),
  *               @OA\Property(
  *                      property="payment_intent_id",
  *                      type="string",
  *                      example="pi_3Nbr...MD0nXVeXaX",
  *                      description="A stripe payment intent id"
  *              ),
  *               @OA\Property(
  *                      property="client_secret",
  *                      type="string",
  *                      example="pi_3NG4brK..._secret_...hUfR2",
  *                      description="A stripe client secret"
  *              ),
  *              @OA\Property(
  *                      property="amount",
  *                      type="integer",
  *                      example="100"
  *              ),
  *              @OA\Property(
  *                      property="currency",
  *                      type="string",
  *                      example="eur"
  *              ),
  *              @OA\Property(
  *                      property="payment_method_types",
  *                      type="array",
  *                      @OA\Items(),
  *                      example={"card"}
  *              ),
  *              @OA\Property(
  *                      property="messages",
  *                      type="array",
  *                      @OA\Items(),
  *              ),
  *          ),
  *     ),
  *     @OA\Response(
  *          response="400",
  *          description="Product add error",
  *          @OA\JsonContent(
  *            ref="#/components/schemas/baseerror",
  *          ),
  *     ),
  * )
  */
    public function createPaymentIntent(Request $request)
    {
        $userid = auth()->user()->id;
        $dado = $request->all();
        // ['amount' => 1099,
        // 'currency' => 'usd', 'payment_method_types' => ['card']]
        // ['beamer_id' => ] <-- the payment needs this to do splits of values
        // ['call_id' => ] <-- optional_to_end_process of call
        $dado = $request->all();       
        $validator = Validator::make($dado, [
            'call_id' => 'required|exists:videocalls,id',
            'amount' => 'required|integer',
            'currency' => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }

        $call_id = $dado['call_id'];        
        $products = VideocallProducts::where('videocall_id', $call_id)->where('status', 'accepted')->get();
        $dado['amount_products'] = count($products);
        $videocall = Videocall::where('id', $call_id)->first();

        $call_cost =  (int) $dado['amount'];
        $checkout = (new Checkout($products, $call_id, $userid, $call_cost))->get();

        // get application FEE
        $commission_to_bb = Param::getParam('commission_to_bb');
        // soma amount
        $fullamount =  $checkout['checkout']['total_cost'];
        $dado['amount'] = $fullamount;
        $commission_cents = ((float) $commission_to_bb * $fullamount) / 100;
        $commission_cents = round($commission_cents);
        if($commission_cents == 0) {
            $commission_cents = 1;
        }
        $dado['application_fee_amount'] = $commission_cents;
        $dado['beamer_id'] = $videocall->beamer_id;

        $payment_intent = Payment::createPaymentIntent($userid, $dado);
        unset($dado['application_fee_amount']);
        $payment_intent += $dado;
        // pi_3N6a55K6bwyRYOMD00lwM9CL
        $payment_intent['messages'] = [];
        return response()->json($payment_intent, 200);
    }

/**
  * @OA\Post(
  *     path="/payments/paycall",
  *     tags={"payments 🔒"},
  *     @OA\RequestBody(
  *          @OA\MediaType(
  *              mediaType="multipart/form-data",
  *              @OA\Schema(
  *                  @OA\Property(
  *                      property="call_id",
  *                      type="integer",
  *                  ),
  *                  @OA\Property(
  *                      property="amount",
  *                      type="integer",
  *                      description="call amount in cents",
  *                  ),
  *                  @OA\Property(
  *                      property="use_wallet_amount",
  *                      type="integer",
  *                      description="use wallet as part of payment (in cents)",
  *                  ),
  *                  @OA\Property(
  *                      property="currency",
  *                      type="string",
  *                  ),
  *              )
  *          )
  *     ),
  *     @OA\Response(
  *          response="200",
  *          description="Get a status of a added product",
  *          @OA\JsonContent(
  *              @OA\Schema(ref="#/components/schemas/shoppingsingle"),
  *          ),
  *     ),
  *     @OA\Response(
  *          response="400",
  *          description="Product add error",
  *          @OA\JsonContent(
  *            ref="#/components/schemas/baseerror",
  *          ),
  *     ),
  * )
  */
    public function makePayCall(Request $request)
    {
        $userid = auth()->user()->id;
        $dado = $request->all();
        $dado['currency'] = $request->get('currency', 'eur');
        $dado['use_wallet_amount'] = (int) $request->get('use_wallet_amount', 0);
        $validator = Validator::make($dado, [
            'call_id' => 'required|exists:videocalls,id',
            'amount' => 'required|integer',
            'currency' => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }

        // ['amount' => 1099,
        // 'currency' => 'usd',
        // 'payment_method_types' => ['card']]
        // ['beamer_id' => ] <-- the payment needs this to do splits of values

        // get application FEE
        $commission_to_bb = Param::getParam('commission_to_bb');
        
        $call_id = $dado['call_id'];        
        $products = VideocallProducts::where('videocall_id', $call_id)->where('status', 'accepted')->get();
        $dado['amount_products'] = count($products);
        $videocall = Videocall::where('id', $call_id)->first();

        $call_cost =  (int) $dado['amount'];
        $use_wallet_amount =  (int) $dado['use_wallet_amount'];
        $checkout = (new Checkout($products, $call_id, $userid, $call_cost, $use_wallet_amount))->get();
        dblog('checkout', json_encode($checkout));
        // soma amount
        $fullamount =  $checkout['checkout']['total_cost'];
        $dado['amount'] = $fullamount;
        $commission_cents = ((float) $commission_to_bb * $fullamount) / 100;
        $commission_cents = round($commission_cents);
        if($commission_cents == 0) {
            $commission_cents = 1;
        }
        $dado['application_fee_amount'] = $commission_cents;
        $dado['beamer_id'] = $videocall->beamer_id;

        $payment = Payment::createPayCall($userid, $dado);
        unset($dado['application_fee_amount']);
        $payment += $dado;
        // pi_3N6a55K6bwyRYOMD00lwM9CL
        $payment['messages'] = [];
        return response()->json($payment, 200);
    }

    public function postWebhook(Request $request)
    {
        // todo implet header verification
        // whsec_9yOmexpjAEafbwQEGAHydKOAB1aACQ8l
        $dado = $request->all();
        $webHook_proccess = Payment::webHook($dado);
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }
}
