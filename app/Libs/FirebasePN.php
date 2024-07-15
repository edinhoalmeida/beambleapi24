<?php 
namespace App\Libs;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Topic;
use Kreait\Firebase\Exception\Messaging\InvalidMessage as TokenInvalidMessage;

use App\Http\Resources\VideocallProduct as VideocallProductResource;

use App\Exceptions\FirebaseException;
use App\Models\UserDevices;
use App\Libs\FirebaseDB;

class FirebasePN {

    private $factory = null;
    private $messaging = null;

    public function __construct()
    {
        $file_settings = env('FIREBASE_CREDENTIALS');
        $file_set = base_path($file_settings);
        if(file_exists($file_set)){
            $this->factory = (new Factory)->withServiceAccount($file_set);
            $this->messaging = $this->factory->createMessaging();
        } else {
            throw new FirebaseException('Firebase credentials not found.');
        }
    }

    public function add_user_to_topic($user_firebase_token)
    {
        $this->messaging->subscribeToTopic("allusers", $user_firebase_token);
    }

    public function remove_users_from_topic($firebase_tokens)
    {
        // You can subscribe up to 1,000 devices in a single request.
        $pieces = array_chunk($firebase_tokens, 1000);
        foreach ($pieces as $piece) {
            try {
                $this->messaging->unsubscribeFromAllTopics($piece);
            } catch (Exception $e){
                //
            }
        }
    }

    public function validate_tokens($firebase_tokens)
    {
        return $this->messaging->validateRegistrationTokens($firebase_tokens);
    }

    public function send_map_changed($data)
    {
        $message = CloudMessage::withTarget('topic', 'allusers')
            // ->withNotification(Notification::create($title, 'Body'))
            ->withData(['event' => 'map_view_changed']);
        $this->messaging->send($message);
        // throw new FirebaseException('Firebase all users push not implemented yet.');
    }

/**
    * @OA\Options(
    *     path="/firebase-on-google/client-ask",
    *     tags={"push notifications"},
    *     description="This path is not used. It is only do documentation porpouse.",
    *     @OA\Response(
    *          response="200",
    *          description="Object 'data' send with push notifications",
    *          @OA\JsonContent(
    *                 @OA\Property(
    *                   property="data",
    *                   type="object",
    *                   description="object send with push notification",
    *                     @OA\Property(
    *                       property="event",
    *                       type="string",
    *                       description="event name"
    *                     ),
    *                     @OA\Property(
    *                       property="url",
    *                       type="string",
    *                       description="url to use in app routes",
    *                       example="beamble://lobby/[call_id]"
    *                     ),
    *                     @OA\Property(
    *                       property="callId",
    *                       type="number",
    *                       description="callId"
    *                     ),
    *                     @OA\Property(
    *                       property="client_name",
    *                       type="string",
    *                       description="name of client"
    *                     ),
    *                     @OA\Property(
    *                       property="beamer_id",
    *                       type="number",
    *                       description="beamer_id"
    *                     ),
    *                     @OA\Property(
    *                       property="client_id",
    *                       type="number",
    *                       description="client_id"
    *                     ),
    *                 ),
    *          ),
    *     ),
    * )
    */
    public function send_client_ask($target_token, $client_name, $title, $call_id, $beamer_id, $client_id)
    {
        $notification_data = [
            'event' => 'client_ask',
            'callId' => $call_id,
            'beamer_id'=> $beamer_id,
            'client_id'=> $client_id,
            'url' => 'beamble://lobby/' . $call_id,
            'client_name' => $client_name,
        ];
        $message = CloudMessage::fromArray([
            'token' => $target_token,
            'notification' => [ // optional
                "title" => $title,
                "body" => $client_name
            ], // optional
            'data' => $notification_data, // optional
        ]);

        try {
            $this->messaging->send($message);
        } catch(TokenInvalidMessage $e){
            UserDevices::where('firebase_token', $target_token)->delete();
            return false;
        } catch(\Exception $e){
            return false;
        }
        return true;
    }

/**
    * @OA\Options(
    *     path="/firebase-on-google/beamer-accepted",
    *     tags={"push notifications"},
    *     description="This path is not used. It is only do documentation porpouse.",
    *     @OA\Response(
    *          response="200",
    *          description="Object 'data' send with push notifications",
    *          @OA\JsonContent(
    *                 @OA\Property(
    *                   property="data",
    *                   type="object",
    *                   description="object send with push notification",
    *                     @OA\Property(
    *                       property="event",
    *                       type="string",
    *                       description="event name",
    *                       example="beamer_accept"
    *                     ),
    *                     @OA\Property(
    *                       property="url",
    *                       type="string",
    *                       description="url to use in app routes",
    *                       example="beamble://premeeting"
    *                     ),
    *                     @OA\Property(
    *                       property="callId",
    *                       type="number",
    *                     ),
    *                     @OA\Property(
    *                       property="client_id",
    *                       type="number",
    *                     ),
    *                     @OA\Property(
    *                       property="beamer_id",
    *                       type="number",
    *                     ),
    *                     @OA\Property(
    *                       property="meeting_id",
    *                       type="string",
    *                       example="awsada-qawead-asasd"
    *                     ),
    *                     @OA\Property(
    *                       property="meeting_object",
    *                       type="object",
    *                       ref="#/components/schemas/meetingobject",
    *                     ),
    *                 ),
    *          ),
    *     ),
    * )
    */
    public function send_beamer_accept($target_token, $client_name, $title, $videocall)
    {
        $send_data = [
            'event' => 'beamer_accept',
            'callId' => $videocall->id,
            'client_id' => $videocall->client_id,
            'beamer_id' => $videocall->beamer_id,
            'url' => 'beamble://premeeting',
            'meeting_id' => $videocall->meeting_id,
            'meeting_object' => (string) $videocall->meeting_object,
        ];
        dblog('send_beamer_accept token', $target_token);

        $message = CloudMessage::fromArray([
            'token' => $target_token,
            'notification' => [ // optional
                "title" => $title,
                "body" => $client_name
            ], // optional
            'data' => $send_data, // optional
        ]);


        try {
            $this->messaging->send($message);
        } catch(TokenInvalidMessage $e){
            dblog('send_beamer_accept token TokenInvalidMessage', '');
            UserDevices::where('firebase_token', $target_token)->delete();
            return false;
        } catch(\Exception $e){
            dblog('send_beamer_accept token Exception', $e->getMessage());
            return false;
        }
        dblog('send_beamer_accept token ok', '');
        return true;
    }

/**
    * @OA\Options(
    *     path="/firebase-on-google/product-addded",
    *     tags={"push notifications"},
    *     description="This path is not used. It is only do documentation porpouse.",
    *     @OA\Response(
    *          response="200",
    *          description="Object 'data' send with push notifications",
    *          @OA\JsonContent(
    *                 @OA\Property(
    *                   property="data",
    *                   type="object",
    *                   description="object send with push notification",
    *                     @OA\Property(
    *                       property="event",
    *                       type="string",
    *                       description="event name",
    *                       example="beamer_product_offer"
    *                     ),
    *                     @OA\Property(
    *                       property="callId",
    *                       type="integer",
    *                     ),
    *                     @OA\Property(
    *                       property="url",
    *                       type="string",
    *                       description="url to use in app routes",
    *                       example="beamble://cart"
    *                     ),
    *                     @OA\Property(
    *                       property="product",
    *                       type="object",
    *                       ref="#/components/schemas/product"  
    *                     ),
    *                 ),
    *          ),
    *     ),
    * )
    */
    public function send_product_offered($target_token, $client_name, $title, $videocall, $product)
    {
        $product = new VideocallProductResource($product);

        $message = CloudMessage::fromArray([
            'token' => $target_token,
            'notification' => [ // optional
                "title" => $title,
                "body" => $client_name
            ], // optional
            'data' => [
                'event' => 'beamer_product_offer',
                'callId' => $videocall->id,
                'url' => 'beamble://cart',
                'product' => json_encode($product)
            ], // optional
        ]);
        try {
            $this->messaging->send($message);
        } catch(TokenInvalidMessage $e){
            dblog('product_offered token problem', "OOPS");
            UserDevices::where('firebase_token', $target_token)->delete();
            return false;
        } catch(\Exception $e){
            return false;
        }
        dblog('product_offered ok', "Yes");
        return true;
    }

    public function all($data)
    {
        // $message = CloudMessage::withTarget(/* see sections below */)
        //     ->withNotification(Notification::create('Title', 'Body'))
        //     ->withData(['message' => 'map_view_changed']);

        // $messaging->send($message);
        // throw new FirebaseException('Firebase all users push not implemented yet.');
    }

    public function one($dados)
    {
        $this->add_user_to_topic($dados['target_token']);
        $message = CloudMessage::fromArray([
            'token' => $dados['target_token'],
            'notification' => [ // optional
                "title" => "Beamble Notification by API",
                "body" => $dados['target_message']
            ], // optional
            'data' => [/* data array */], // optional
        ]);
        try {
            $this->messaging->send($message);
        } catch(TokenInvalidMessage $e){
            UserDevices::where('firebase_token', $target_token)->delete();
            return false;
        } catch(\Exception $e){
            return false;
        }
        return true;
    }

/**
    * @OA\Options(
    *     path="/firebase-on-google/beamer-rejected",
    *     tags={"push notifications"},
    *     description="This path is not used. It is only do documentation porpouse.",
    *     @OA\Response(
    *          response="200",
    *          description="Object 'data' send with push notifications",
    *          @OA\JsonContent(
    *                 @OA\Property(
    *                   property="data",
    *                   type="object",
    *                   description="object send with push notification",
    *                     @OA\Property(
    *                       property="event",
    *                       type="string",
    *                       description="event name",
    *                       example="beamer_reject"
    *                     ),
    *                     @OA\Property(
    *                       property="url",
    *                       type="string",
    *                       description="url to use in app routes",
    *                       example="beamble://home"
    *                     ),
    *                 ),
    *          ),
    *     ),
    * )
    */
    public function send_beamer_reject($target_token, $client_name, $title, $videocall)
    {

        $send_data = [
            'event' => 'beamer_reject',
            'url' => 'beamble://home',
            'callId' => $videocall->id,
            'client_id' => $videocall->client_id,
            'beamer_id' => $videocall->beamer_id
        ];
        $message = CloudMessage::fromArray([
            'token' => $target_token,
            'notification' => [ // optional
                "title" => $title,
                "body" => $client_name
            ], // optional
            'data' => $send_data
        ]);

        
        try {
            $this->messaging->send($message);
        } catch(TokenInvalidMessage $e){
            UserDevices::where('firebase_token', $target_token)->delete();
            return false;
        } catch(\Exception $e){
            return false;
        }
        return true;
    }

/**
    * @OA\Options(
    *     path="/firebase-on-google/call-timer",
    *     tags={"push notifications"},
    *     description="This path is not used. It is only do documentation porpouse.",
    *     @OA\Response(
    *          response="200",
    *          description="Object 'data' send with push notifications",
    *          @OA\JsonContent(
    *                 @OA\Property(
    *                   property="data",
    *                   type="object",
    *                   description="object send with push notification",
    *                     @OA\Property(
    *                       property="event",
    *                       type="string",
    *                       description="event name",
    *                       example="timer_start"
    *                     ),
    *                     @OA\Property(
    *                       property="callId",
    *                       type="integer",
    *                     ),
    *                     @OA\Property(
    *                       property="url",
    *                       type="string",
    *                       description="url to use in app routes",
    *                       example="beamble://meeting"
    *                     ),
    *                 ),
    *          ),
    *     ),
    * )
    */
    public function send_call_timer($target_token, $sender_name, $title, $videocall, $action_name)
    {
        $message = CloudMessage::fromArray([
            'token' => $target_token,
            'notification' => [ // optional
                "title" => $title,
                "body" => $sender_name
            ], // optional
            'data' => [
                'event' => $action_name,
                'callId' => $videocall->id,
                'url' => 'beamble://meeting',
            ], // optional
        ]);
        try {
            $this->messaging->send($message);
        } catch(TokenInvalidMessage $e){
            UserDevices::where('firebase_token', $target_token)->delete();
            return false;
        } catch(\Exception $e){
            return false;
        }
        return true;
    }

}