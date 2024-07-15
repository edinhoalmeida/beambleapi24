<?php

namespace App\Http\Controllers\API;

use App\Jobs\ProcessImage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\UserDevices;
use App\Models\ImageB64 as ImageModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;

use App\Providers\UserServiceProvider;
use App\Http\Requests\ClientRequest2 as ClientRequest;
use App\Http\Requests\ClientRequestSimple;
use App\Http\Requests\BeamerRequest2 as BeamerRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\NewOTPRequest;
use App\Http\Requests\VerifyOTPRequest;

use App\Http\Resources\User as UserResource;

use App\Libs\WorldAddress;
use App\Libs\FirebasePN;

use App\Models\UserPolyData;


class RegisterController extends BaseController
{
    private $user_repo = null;

    public function __construct(UserServiceProvider $user_repo)
    {
        $this->user_repo = $user_repo;
    }

    public function templateotp(Request $request)
    {
        $dados = [
            'otp_code' => '1234'
        ];
        return view('emails.newotp', $dados);
    }




/**
     * @OA\Post(
    *     path="/already_registered",
    *     tags={"register"},
    *     summary="Verify if email already_registered",
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="email",
    *                      type="string",
    *                  ),
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Email avaiable",
    *          @OA\JsonContent(
    *                   @OA\Property(
    *                       property="success",
    *                       type="boolean",
    *                       description="Status of request"
    *                    ),
    *                   @OA\Property(
    *                       property="data",
    *                       type="object",
    *                       @OA\Property(
    *                            property="available",
    *                            type="boolean",
    *                        ),
    *                   ),
    *                   @OA\Property(
    *                       property="message",
    *                       type="string",
    *                       description="OK"
    *                   ),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Verify already registered",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function already_registered(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email|max:50'
        ]);
        if($validator->fails()) {
            return $this->sendError(__('beam.user_register_error'), $validator->errors());
        }
        $success['available'] = true;
        return $this->sendResponse($success, "OK");
    }

    /**
      * @OA\Post(
      *     path="/login",
      *     tags={"users"},
      *     summary="Login route",
      *     @OA\RequestBody(
      *          @OA\MediaType(
      *              mediaType="multipart/form-data",
      *              @OA\Schema(
      *                  @OA\Property(
      *                      property="email",
      *                      type="string",
      *                  ),
      *                  @OA\Property(
      *                      property="password",
      *                      type="string",
      *                  ),
      *                  @OA\Property(
      *                      property="interface_as",
      *                      type="string",
      *                  ),
      *                  @OA\Property(
      *                      property="firebase_token",
      *                      type="string",
      *                      description="Firebase device token",
      *                  ),
      *              )
      *          )
      *      ),
      *     @OA\Response(
      *          response="200",
      *          description="Login success",
      *          @OA\JsonContent(
      *            ref="#/components/schemas/baseusers",
      *          ),
      *     ),
      *     @OA\Response(
      *          response="400",
      *          description="Login error",
      *          @OA\JsonContent(
      *            ref="#/components/schemas/baseerror",
      *          ),
      *     ),
      * )
      */
    public function login(Request $request)
    {
        $input = $request->all();
        dblog('login  ....', json_encode($input));
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->fails()) {
            return $this->sendError(__('beam.user_register_error'), $validator->errors());
        }

        if(Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
            $user = Auth::user();
            $firebase_token = $request->get('firebase_token');
            if($firebase_token) {
                $FB = new FirebasePN();
                $FB->add_user_to_topic($firebase_token);
                UserDevices::add_device($user->id, $firebase_token);
            }

            $success['token'] =  $user->createToken('Beam')->plainTextToken;
            $success['name'] =  $user->name;
            $success['permissions'] = $user->get_permissions();
            $interface_def = empty($user->interface_as) ? 'client' : $user->interface_as;
            $success['interface_as'] = $request->get('interface_as', $interface_def);

            $user->interface_as = $success['interface_as'];
            $user->save();

            // $success['permissions'] = \permissoes_user2interface( $success['permissions'] );
            $success['user'] = new UserResource($user);

            return $this->sendResponse($success, __('beam.user_login_success'));
        } else {
            return $this->sendError(__('beam.user_login_error'), ['error' => 'Unauthorised'], 401);
        }
    }

    public function forget(Request $request)
    {
        // if( ! $request->ajax() ){
        //     return [];
        // }
        $a = ['email' => 'edinhoalmeida@gmail.com'];
        $save = $this->user_repo->forget($a);
        // $save = $this->user_repo->forget($request->all());
        if($save['success']) {
            return $this->sendResponse([], __('beam.user_reset_success'));
        } else {
            return $this->sendError(__('beam.user_reset_error'));
        }
    }

/**
     * @OA\Post(
    *     path="/register_client_simple",
    *     tags={"register"},
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(ref="#/components/schemas/clientrequestsimple")
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="User registred successfully",
    *          @OA\JsonContent(
    *             ref="#/components/schemas/user"
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Register error",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/validationerror",
    *          ),
    *     ),
    * )
    */
    public function register_client_simple(ClientRequestSimple $request)
    {
        $input = $request->all();
        dblog('register_client_simple', json_encode($input));
        $input_user = $request->only(['name', 'email', 'password', 'surname']);
        $input_user['tos_accepted_at'] =
            empty($input['tos_accepted']) ? null : date('Y-m-d H:i:s', time());
        $input_user['password'] = bcrypt($input_user['password']);
        $input_user['uuid'] = Str::uuid()->toString();
        $input_user['interface_as'] = "client";
        $input_user['is_generic'] = 0;

        // Client Type
        $roleType = 'Client';
        $user = User::create($input_user);

        //copy data to client_table
        UserPolyData::set_client_data($user->id, $input_user);

        $user->assignRole($roleType);
        $user->markEmailAsVerified();

        $firebase_token = $request->get('firebase_token');
        if($firebase_token) {
            $FB = new FirebasePN();
            $FB->add_user_to_topic($firebase_token);
            UserDevices::add_device($user->id, $firebase_token);
        }

        if(empty($input['ui'])) {
            $success['token'] =  $user->createToken('Beam')->plainTextToken;
        } elseif($input['ui'] != 'web') {
            $success['token'] =  $user->createToken('Beam')->plainTextToken;
        }
        $success['name'] =  $user->name;
        $success['user_id'] = $user->id;

        $success['user'] = new UserResource($user);

        return $this->sendResponse($success, __('beam.user_register_success'));
    }

    /**
      * @OA\Post(
      *     path="/register_client",
      *     tags={"register"},
      *     @OA\RequestBody(
      *          @OA\MediaType(
      *              mediaType="multipart/form-data",
      *              @OA\Schema(ref="#/components/schemas/clientrequest2")
      *          )
      *      ),
      *     @OA\Response(
      *          response="200",
      *          description="Get follow status of a user",
      *          @OA\JsonContent(
      *             oneOf={
      *                  @OA\Schema(ref="#/components/schemas/validationerror"),
      *                  @OA\Schema(ref="#/components/schemas/user"),
      *             },
      *          ),
      *     ),
      *     @OA\Response(
      *          response="400",
      *          description="Register error",
      *          @OA\JsonContent(
      *            ref="#/components/schemas/baseerror",
      *          ),
      *     ),
      * )
      */
    public function register_client(ClientRequest $request)
    {
        $input = $request->all();
        $input_user = $request->only(['name','email','password', 'phone']);
        $input_user['tos_accepted_at'] =
            empty($input['tos_accepted']) ? null : date('Y-m-d H:i:s', time());
        if(empty($input['accept_parcel_return'])) {
            $input_user['accept_parcel_return'] = 0;
        } else {
            $input_user['accept_parcel_return'] = 1;
        }
        $input_user['password'] = bcrypt($input_user['password']);
        $input_user['uuid'] = Str::uuid()->toString();
        $input_user['interface_as'] = "client";
        $input_user['is_generic'] = 0;

        // Client Type
        $roleType = 'Client';
        $user = User::create($input_user);
        $user->assignRole($roleType);

        $firebase_token = $request->get('firebase_token');
        if($firebase_token) {
            $FB = new FirebasePN();
            $FB->add_user_to_topic($firebase_token);
            UserDevices::add_device($user->id, $firebase_token);
        }

        // se não estiver vazio grava idiomas
        $languages = $input['my_language'];
        if(empty($languages)) {
            $ar = [['lang_code' => 'fr']];
            $user->lang()->createMany($ar);
        } elseif(is_string($languages)) {
            $ar = [['lang_code' => $languages]];
            $user->lang()->createMany($ar);
        } else {
            $ar = [];
            foreach($languages as $l) {
                $ar[] = ['lang_code' => $l];
            }
            $user->lang()->createMany($ar);
        }


        $input_address = $request->only(['address','postal_code','city', 'country']);
        $input_address['address_type'] = empty($input_address['address_type']) ? 'contact' : $input_address['address_type'];
        $user->address()->create($input_address);

        $this->user_repo->generate_otp(['email' => $input['email']]);

        if(empty($input['ui'])) {
            $success['token'] =  $user->createToken('Beam')->plainTextToken;
        } elseif($input['ui'] != 'web') {
            $success['token'] =  $user->createToken('Beam')->plainTextToken;
        }
        $success['name'] =  $user->name;
        $success['user_id'] = $user->id;

        return $this->sendResponse($success, __('beam.user_register_success'));
    }

    /**
      * @OA\Post(
      *     path="/register_beamer",
      *     tags={"register"},
      *     @OA\RequestBody(
      *          @OA\MediaType(
      *              mediaType="multipart/form-data",
      *              @OA\Schema(ref="#/components/schemas/beamerrequest2")
      *          )
      *      ),
      *     @OA\Response(
      *          response="200",
      *          description="Get follow status of a user",
      *          @OA\JsonContent(
      *             ref="#/components/schemas/user",
      *          ),
      *     ),
      *     @OA\Response(
      *          response="400",
      *          description="Register error",
      *          @OA\JsonContent(
      *            ref="#/components/schemas/baseerror",
      *          ),
      *     ),
      * )
      */
    public function register_beamer(BeamerRequest $request)
    {
        $input = $request->all();
        $input_user = $request->only(['name','level_expertise','second_hand_resaler','surname','email','password', 'phone','website', 'company_name', 'company_doc', 'company_type']);
        $input_user['tos_accepted_at'] =
            empty($input['tos_accepted']) ? null : date('Y-m-d H:i:s', time());
        if(empty($input['accept_parcel_return'])) {
            $input_user['accept_parcel_return'] = 0;
        } else {
            $input_user['accept_parcel_return'] = 1;
        }
        if(empty($input['second_hand_resaler'])) {
            $input_user['second_hand_resaler'] = 0;
        } else {
            $input_user['second_hand_resaler'] = 1;
        }
        if(empty($input['level_expertise'])) {
            $input_user['level_expertise'] = null;
        }
        $input_user['password'] = bcrypt($input_user['password']);
        $input_user['uuid'] = Str::uuid()->toString();
        $input_user['interface_as'] = "beamer";
        $input_user['is_generic'] = 0;

        // Client Type
        $roleType = 'Beamer';
        $user = User::create($input_user);
        UserPolyData::set_beamer_data($user->id, $input_user);

        $user->assignRole($roleType);

        $firebase_token = $request->get('firebase_token');
        if($firebase_token) {
            $FB = new FirebasePN();
            $FB->add_user_to_topic($firebase_token);
            UserDevices::add_device($user->id, $firebase_token);
        }

        // se não estiver vazio grava idiomas
        $languages = $input['my_language'];
        if(empty($languages)) {
            $ar = [['lang_code' => 'fr']];
            $user->lang()->createMany($ar);
        } elseif(is_string($languages)) {
            $ar = [['lang_code' => $languages]];
            $user->lang()->createMany($ar);
        } else {
            $ar = [];
            foreach($languages as $l) {
                $ar[] = ['lang_code' => $l];
            }
            $user->lang()->createMany($ar);
        }

        $contact_types = ['store'];
        foreach($contact_types as $add_type) {
            $to_save = [];
            foreach($input as $chave_val => $val_val) {
                if(strpos($chave_val, $add_type) === 0) {
                    $k = str_replace($add_type.'_', '', $chave_val);
                    $to_save['address_type'] = 'contact';// $add_type;
                    $to_save[$k] = $val_val;
                }
            }
            // print_r($to_save);
            if(!empty($to_save)) {
                $cols = collect($to_save);
                $to_latlng = $cols->only('address', 'city', 'country', 'postal_code')->toArray();
                $wa = new WorldAddress(implode(", ", $to_latlng));
                $latlng = $wa->request_lat_lng();
                if(isset($latlng['lat'])) {
                    $to_save =  array_merge($to_save, $latlng);
                }
                $user->address()->create($to_save);
            }
        }

        // grava imagem
        if(!empty($input['image_foto'])) {
            $imageable = new ImageModel([
                'base64' => $input['image_foto'],
                'modifier_id' => $user->id,
                'type' => 'profile',
            ]);
            $user->allimages()->save($imageable);
            ProcessImage::dispatch($imageable)->onQueue('videos');
        }
        // grava logo
        if(!empty($input['image_logo'])) {
            $imageable2 = new ImageModel([
                'base64' => $input['image_logo'],
                'modifier_id' => $user->id,
                'type' => 'logo',
            ]);
            $user->allimages()->save($imageable2);
        }

        // auto onboarding
        $user->beamer()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'account_id' => 'acct_1NBP0C2eKu3NL3Uj',
                'account_stripe_enabled' => 1,
                'account_token' => ''
            ]
        );

        // $this->user_repo->generate_otp(['email' => $input['email']]);
        $user->markEmailAsVerified();

        if(empty($input['ui'])) {
            $success['token'] =  $user->createToken('Beam')->plainTextToken;
        } elseif($input['ui'] != 'web') {
            $success['token'] =  $user->createToken('Beam')->plainTextToken;
        }
        $success['name'] =  $user->name;
        $success['user_id'] = $user->id;

        $success['user'] = new UserResource($user);

        return $this->sendResponse($success, __('beam.user_register_success'));
    }

    public function change_pass(Request $request)
    {
        $input = $request->all();
        $user = User::where('email', $input['email'])->first();
        $user->password = bcrypt($input['password']);
        $user->save();
        return $this->sendResponse([], __('beam.user_register_success'));
    }

    /**
      * @OA\Post(
      *     path="/register/newotp",
      *     tags={"register"},
      *     @OA\RequestBody(
      *          @OA\MediaType(
      *              mediaType="multipart/form-data",
      *              @OA\Schema(
      *                  @OA\Property(
      *                      property="email",
      *                      type="string",
      *                  ),
      *              )
      *          )
      *      ),
      *     @OA\Response(
      *          response="200",
      *          description="Generate and send by email a six digit number",
      *          @OA\JsonContent(
      *             oneOf={
      *                  @OA\Schema(ref="#/components/schemas/validationerror"),
      *                  @OA\Schema(ref="#/components/schemas/base"),
      *             },
      *          ),
      *     )
      * )
      */
    public function new_otp(NewOTPRequest $request)
    {
        $input = $request->only(['email']);
        $this->user_repo->generate_otp_pre($input);
        return $this->sendResponse([], __('beam.user_register_otp'));
    }


    /**
      * @OA\Post(
      *     path="/register/verifyotp",
      *     tags={"register"},
      *     @OA\RequestBody(
      *          @OA\MediaType(
      *              mediaType="multipart/form-data",
      *              @OA\Schema(
      *                  @OA\Property(
      *                      property="email",
      *                      type="string",
      *                  ),
      *                  @OA\Property(
      *                      property="number_otp",
      *                      type="string",
      *                  ),
      *              )
      *          )
      *      ),
      *     @OA\Response(
      *          response="200",
      *          description="Generate and send by email a six digit number",
      *          @OA\JsonContent(
      *             oneOf={
      *                  @OA\Schema(ref="#/components/schemas/validationerror"),
      *                  @OA\Schema(ref="#/components/schemas/base"),
      *             },
      *          ),
      *     ),
      *     @OA\Response(
      *          response="400",
      *          description="OTP verify error",
      *          @OA\JsonContent(
      *            ref="#/components/schemas/baseerror",
      *          ),
      *     ),
      * )
      */
    public function verify_otp(VerifyOTPRequest $request)
    {
        $input = $request->only(['email','number_otp']);
        $verification = $this->user_repo->verify_otp_pre($input);
        if($verification['success']) {
            // $user = User::where('email', $input['email'])->first();
            // $verification['user'] = new UserResource($user);
            return $this->sendResponse($verification, __('beam.user_register_otp'));
        } else {
            return $this->sendError(__('beam.user_register_otp_error'));
        }
    }

}
