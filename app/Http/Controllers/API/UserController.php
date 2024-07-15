<?php

namespace App\Http\Controllers\API;

use App\Jobs\ProcessImage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\User;
use App\Models\ImageB64 as ImageModel;
use App\Models\Role as RoleModel;
use App\Models\UserSocialmedia;
use App\Models\UserTracking;
use App\Models\UserTrack;
use App\Models\UserVideofeed;
use App\Models\UserPolyData;

use App\Jobs\ProcessVideo;
use App\Libs\Beamer;
use DB;

use App\Http\Requests\ClientUpdate;
use App\Http\Requests\BeamerUpdate;

use Validator;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Pins as PinsResource;
use App\Http\Resources\Profile as ProfileResource;
use App\Http\Resources\UserSimple as UserSimpleResource;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Arr;

use Payment;

use App\Events\MapChanged;
use App\Libs\WorldAddress;

use Illuminate\Support\Facades\Cache;

use App\Libs\FirebasePN;
use App\Models\UserDevices;

class UserController extends BaseController
{

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // $request->input('page', 1);
        // $data = User::orderBy('id','DESC')->paginate(10);
        // return $this->sendResponse(UserResource::collection($data), __('beam.users_list_success'));

        $users = User::all();

        $dados = [];
        $dados['users'] = UserResource::collection($users);

        return $this->sendResponse($dados, __('beam.users_list_success'));
    }

    public function create()
    {
        $dados = [];
        $dados['roles'] = RoleModel::getRolesToInterface();
        return $this->sendResponse($dados, __('beam.users_create') );
    }

    public function show($id, $interface_as)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError( __('beam.users_not_found') );
        }
        if( in_array($interface_as, ['client','beamer']) ){
            $user->interface_as = $interface_as;
            $user->save();
        }
        $dados = [];
        $dados['user'] = new UserResource($user);
        return $this->sendResponse($dados, __('beam.users_show') );
    }

/**
    * @OA\Post(
    *     path="/me",
    *     tags={"users"},
    *     summary="Requires authentication",
    *     security={ {"bearerAuth": {}} },
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="firebase_token",
    *                      type="string",
    *                  ),
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Sumary of user properties",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="user",
    *                      type="object",
    *                      ref="#/components/schemas/user"
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Auth error. Header Baerer is required.",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function me(Request $request)
    {
        $userid = auth()->user()->id;
        $firebase_token = $request->get('firebase_token');
        dblog('me route', json_encode($request->all()));
        if($firebase_token) {
            $FB = new FirebasePN();
            $FB->add_user_to_topic($firebase_token);
            UserDevices::change_device($userid, $firebase_token);
        }
        return $this->show($userid, null);
    }

/**
    * @OA\Get(
    *     path="/user/status",
    *     tags={"users"},
    *     summary="Requires authentication",
    *     security={ {"bearerAuth": {}} },
    *     @OA\Response(
    *          response="200",
    *          description="Sumary of user properties",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="user",
    *                      type="object",
    *                      ref="#/components/schemas/simpleuser"
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Auth error. Header Baerer is required.",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function status()
    {
        $authuser = auth()->user();
        if (empty($authuser)) {
            return $this->sendError( __('beam.users_not_found'), code: 401);
        }
        $userid = $authuser->id;
        Payment::customerByUserId($userid);

        $user = User::find($userid);
        if (empty($user)) {
            return $this->sendError( __('beam.users_not_found') );
        }
        $dados = [];
        $dados['user'] = new UserSimpleResource($user);
        return $this->sendResponse($dados, __('beam.users_show') );
    }

/**
    * @OA\Get(
    *     path="/users/{user_id}/edit",
    *     tags={"users"},
    *     summary="Requires authentication",
    *     security={ {"bearerAuth": {}} },
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A user id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *    @OA\Parameter(
    *         name="interface_as",
    *         description="Editing as client ou as beamer ?",
    *         in="path",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Response(
    *          response="200",
    *          description="Sumary of user properties",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="user",
    *                      type="object",
    *                      ref="#/components/schemas/user"
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Auth error. Header Baerer is required.",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function edit(Request $request, $user_id, $interface_as = 'client')
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            return $this->show($user_id, $interface_as);
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

/**
    * @OA\Get(
    *     path="/logout",
    *     tags={"users"},
    *     summary="Requires authentication",
    *     security={ {"bearerAuth": {}} },
    *     @OA\Response(
    *          response="200",
    *          description="Logout",
    *          @OA\JsonContent(
    *             @OA\Schema(ref="#/components/schemas/base"),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Auth error. Header Baerer is required.",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function logout()
    {
        $userid = auth()->user()->id;
        UserDevices::remove_all_devices($userid);
        auth()->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'Logout' );
    }

    /**
    * @OA\Post(
    *     path="/users/{user_id}/start_track",
    *     tags={"track 🔒"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="lat",
    *                      type="number",
    *                  ),
    *                  @OA\Property(
    *                      property="lng",
    *                      type="number",
    *                  ),
    *                  @OA\Property(
    *                      property="cost_per_minute",
    *                      type="number",
    *                  ),
    *                  @OA\Property(
    *                      property="event_title",
    *                      type="string",
    *                  ),
    *                  @OA\Property(
    *                      property="categories[]",
    *                      type="array",
    *                      nullable=true,
    *                      @OA\Items(type="integer"),
    *                  )
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Track start with success",
    *          @OA\JsonContent(
    *             @OA\Schema(ref="#/components/schemas/base"),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="user_id incompatible with the  param user_id",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function start_track(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            $user->position = 'on_line';
            $user->save();
            UserTrack::where('user_id', $user_id)->update(['last_one' => 0]);
            UserTrack::where('user_id', $user_id)->delete();

            $keywords = $request->get('keywords');
            if (is_array($keywords)){
                $keywords = implode(' ; ', $keywords);
            }

            $categories = $request->get('categories','');
            if (is_string($categories) && !empty($categories)){
                $categories = [$categories];
            }
            if (is_array($categories) && !empty($categories)) {
                $categories = ':'.implode(':', $categories).':';
            }

            $with_donation = (int) $request->get('with_donation', 0);

            $is_freemium = (int) $request->get('is_freemium', 0);
            $cost_per_minute = (float) $request->get('cost_per_minute', 0.00);

            if($is_freemium==1 || $cost_per_minute==0){
                $is_freemium = 1;
                $cost_per_minute = 0.0;
            }
            if($user->company_type == 'instore'){
                $cost_per_minute = 0.0;
            }

            $user_track_obj = UserTrack::create([
                    'user_id'=>$user_id,
                    'status'=>'on',
                    'last_one'=>1,
                    // default shop
                    'beamer_type' => $request->get('beamer_type',config('thisapp.beamer_type')),
                    'event_title' => $request->get('event_title'),
                    'categories' => $categories,
                    'cost_per_minute' => $cost_per_minute,
                    'with_donation' => $with_donation,
                    'is_freemium' => $is_freemium,
                    'keywords' => $keywords,
                    'lat'=>$request->get('lat'),
                    'lng'=>$request->get('lng'),
                ]);
            UserTracking::create([
                'user_id'=>$user_id,
                'status'=>'start',
                'beamer_type' => $request->get('beamer_type',config('thisapp.beamer_type')),
                'lat'=>$request->get('lat'),
                'lng'=>$request->get('lng'),
            ]);

            // trigger event to map
            // Trigger the event
            // event(new MapChanged($user_track_obj));

            $dados = [];
            return $this->sendResponse($dados,'');
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }
/**
    * @OA\Post(
    *     path="/users/{user_id}/end_track",
    *     description="Beamber turns off line",
    *     tags={"track 🔒"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A user id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="lat",
    *                      type="number",
    *                  ),
    *                  @OA\Property(
    *                      property="lng",
    *                      type="number",
    *                  ),
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="User end track successfully",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/base",
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="user_id or beamer id not recognized",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function end_track(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            $user->position = 'off_line';
            $user->save();
            $track_atual = UserTrack::where('user_id', $user_id)->withTrashed()->first();
            $beamer_type = $track_atual->beamer_type;
            $track_atual->status = 'end';
            $track_atual->save();

            // trigger event to map
            // Trigger the event
            // event(new MapChanged($track_atual));

            // $track_atual->delete();
            UserTrack::remove_pin($user_id);

            UserTracking::create([
                'user_id'=>$user_id,
                'status'=>'end',
                'beamer_type' => $beamer_type,
                'lat'=>$request->get('lat'),
                'lng'=>$request->get('lng'),
            ]);



            UserTracking::where('user_id', $user_id)->delete();
            $dados = [];
            return $this->sendResponse($dados,'');
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

/**
    * @OA\Post(
    *     path="/users/{user_id}/update_track",
    *     tags={"track 🔒"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A user id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="lat",
    *                      type="number",
    *                  ),
    *                  @OA\Property(
    *                      property="lng",
    *                      type="number",
    *                  ),
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="User coords updated successfully",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/base",
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="user_id or beamer id not recognized",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function update_track(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            $user_track = UserTrack::where('user_id', $user_id)->first();
            if(!empty($user_track)){
                $user_track->update([
                    'user_id'=>$user_id,
                    'lat'=>$request->get('lat'),
                    'lng'=>$request->get('lng'),
                ]);
            } else {
                // UserTrack::create([
                //     'user_id'=>$user_id,
                //     'status'=>'on',
                //     'lat'=>$request->get('lat'),
                //     'lng'=>$request->get('lng'),
                // ]);
            }
            UserTracking::where('user_id', $user_id)->where('status', 'on')->delete();
            $track_atual = UserTracking::create([
                'user_id'=>$user_id,
                'status'=>'on',
                'lat'=>$request->get('lat'),
                'lng'=>$request->get('lng'),
            ]);

            // trigger event to map
            // Trigger the event
            //  event(new MapChanged($track_atual));

            $dados = [];
            return $this->sendResponse($dados,'');
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }

        $userid = auth()->user()->id;
        $input['password'] = bcrypt($input['password']);
        $input['modifier_id'] = $userid;

        $user = User::create($input);
        $user->assignRole($request->input('role_id'));

        if(!empty($input['socialmedia'])){
            foreach($input['socialmedia'] as $socialmedia){
                $social = new UserSocialmedia($socialmedia);
                $user->socialmedia()->save($social);
            }
        }

        $dados = [];
        $dados['user'] = new UserResource($user);

        return $this->sendResponse($dados, __('beam.user_register_success'));
    }

/**
  * @OA\Put(
  *     path="/users/{user_id}/update_client",
  *     tags={"register"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A client id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
  *     @OA\RequestBody(
  *          @OA\MediaType(
  *              mediaType="multipart/form-data",
  *              @OA\Schema(ref="#/components/schemas/clientupdate")
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
    public function update_client(ClientUpdate $request, $user_id)
    {
        $userid = auth()->user()->id;
        $logs = [
            'user_id'=>$user_id,
            'logged_user_id'=>$userid
        ];
        $input = $request->all();
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            if (is_null($user)) {
                return $this->sendError( __('beam.users_not_found') );
            }

            $input = $request->all();

            if($request->has('name')){

                $input_client = $request->only(['name','surname']);
                $user->update($input_client);
                UserPolyData::set_client_data($user_id, $input_client);

                $languages = $input['my_language'];
                // se não estiver vazio grava idiomas
                if(empty($languages)){
                    // $ar = [['lang_code'=>'fr']];
                    // $user->save_langs($ar);
                } elseif(is_string($languages)){
                    $ar = [['lang_code'=>$languages]];
                    $user->save_langs($ar);
                } else {
                    $ar = [];
                    foreach($languages as $l){
                        $ar[] = ['lang_code'=>$l];
                    }
                    $user->save_langs($ar);
                }

                return $this->_get_user_to_return($user_id);

            } elseif($request->has('phone')){

                $input_client = $request->only(['phone']);
                $user->update($input_client);
                UserPolyData::set_client_data($user_id, $input_client);
                return $this->_get_user_to_return($user_id);

            } elseif ($request->has('address')){

                $input_address = $request->only(['address', 'city', 'country', 'postal_code']);
                $input_address['address_type'] = empty($input['address_type'])?'contact':$input['address_type'];
                $input_address_type = $input_address['address_type'];
                $filtered = $user->address->filter(function ($value, $key) use($input_address_type) {
                    return $value->address_type == $input_address_type;
                });
                $to_latlng = $request->only('address', 'city', 'country', 'postal_code');
                $wa = new WorldAddress(implode(", ", $to_latlng));
                $latlng=$wa->request_lat_lng();
                if(isset($latlng['lat'])){
                    $input_address = array_merge($input_address, $latlng);
                }
                if($filtered->count()==0){
                    $user->address()->create($input_address);
                } else {
                    $filtered->first()->update($input_address);
                }
                return $this->_get_user_to_return($user_id);

            } elseif ($request->has('image_foto')){

                // grava imagem
                if(!empty($input['image_foto'])){
                    $imageable = new ImageModel([
                        'base64' => $input['image_foto'],
                        'modifier_id' => $user->id,
                        'type' => 'profile',
                    ]);
                    $user->allimages()
                        ->where('modifier_id', $user->id)
                        ->where('type', 'profile')
                        ->delete();
                    $user->allimages()->save($imageable);
                    ProcessImage::dispatch($imageable)->onQueue('videos');
                }
                return $this->_get_user_to_return($user_id);
            } elseif ($request->has('password')){
                $input_client = $request->only(['password']);
                $input_client['password'] = bcrypt($input_client['password']);
                $user->update($input_client);
                return $this->_get_user_to_return($user_id);
            }

            // $languages = $request->input('my_language');
            // unset($input['my_language']);

            // // se não estiver vazio grava idiomas
            // if(empty($languages)){
            //     $ar = [['lang_code'=>'fr']];
            //     $user->save_langs($ar);
            // } elseif(is_string($languages)){
            //     $ar = [['lang_code'=>$languages]];
            //     $user->save_langs($ar);
            // } else {
            //     $ar = [];
            //     foreach($languages as $l){
            //         $ar[] = ['lang_code'=>$l];
            //     }
            //     $user->save_langs($ar);
            // }


            return $this->_get_user_to_return($user_id);

        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

    private function _get_user_to_return($user_id)
    {
        $nuser = User::find($user_id);
        $dados = [];
        $dados['user'] = new UserResource($nuser);
        return $this->sendResponse($dados, __('beam.users_update_success'));
    }
/**
  * @OA\Put(
 *     path="/users/{user_id}/update_beamer",
  *     tags={"register"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
  *     @OA\RequestBody(
  *          @OA\MediaType(
  *              mediaType="multipart/form-data",
  *              @OA\Schema(ref="#/components/schemas/beamerupdate")
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
    public function update_beamer(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        $logs = [
            'user_id'=>$user_id,
            'logged_user_id'=>$userid
        ];
        $input = $request->all();
        // only edit yourself
        if($userid==$user_id){

            dblog('updateuser autorizado', '');

            $user = User::find($user_id);
            if (is_null($user)) {
                return $this->sendError( __('beam.users_not_found') );
            }

            $input = $request->all();
            $input_client = $request->only(['name','surname','password', 'phone','website', 'company_name', 'company_doc', 'company_type']);

            // if(empty($input['accept_parcel_return'])) {
            //     $input_user['accept_parcel_return'] = 0;
            // } else {
            //     $input_user['accept_parcel_return'] = 1;
            // }
            // if(empty($input['second_hand_resaler'])) {
            //     $input_user['second_hand_resaler'] = 0;
            // } else {
            //     $input_user['second_hand_resaler'] = 1;
            // }
            // if(empty($input['level_expertise'])) {
            //     $input_user['level_expertise'] = null;
            // }


            $languages = $request->input('my_language');

            // se não estiver vazio grava idiomas
            if(empty($languages)){
                // $ar = [['lang_code'=>'fr']];
                // $user->save_langs($ar);
            } elseif(is_string($languages)){
                $ar = [['lang_code'=>$languages]];
                $user->save_langs($ar);
            } else {
                $ar = [];
                foreach($languages as $l){
                    $ar[] = ['lang_code'=>$l];
                }
                $user->save_langs($ar);
            }

            $contact_types = ['store'];
            foreach($contact_types as $add_type){
                $to_save = [];
                foreach($input as $chave_val => $val_val){
                    if(strpos($chave_val, $add_type)===0){
                        $k = str_replace($add_type.'_','',$chave_val);
                        $to_save['address_type'] = $add_type;
                        $to_save[$k] = $val_val;
                    }
                }
                // print_r($to_save);
                if(!empty($to_save)){
                    $cols = collect($to_save);
                    $to_latlng = $cols->only('address', 'city', 'country', 'postal_code')->toArray();
                    $wa = new WorldAddress(implode(", ", $to_latlng));
                    $latlng=$wa->request_lat_lng();
                    if(isset($latlng['lat'])){
                        $to_save =  array_merge($to_save, $latlng);
                    }

                    $filtered = $user->address->filter(function ($value, $key) use($add_type) {
                        return $value->address_type == $add_type;
                    });
                    if($filtered->count()==0){
                        $user->address()->create($to_save);
                    } else {
                        $filtered->first()->update($to_save);
                    }
                }
            }

            // grava imagem
            if(!empty($input['image_foto'])){
                $imageable = new ImageModel([
                    'base64' => $input['image_foto'],
                    'modifier_id' => $user->id,
                    'type' => 'profile',
                ]);
                $user->allimages()
                    ->where('modifier_id', $user->id)
                    ->where('type', 'profile')
                    ->delete();
                $user->allimages()->save($imageable);
                ProcessImage::dispatch($imageable)->onQueue('videos');
            }

            // // grava logo
            // if(!empty($input['image_logo'])){
            //     $imageable2 = new ImageModel([
            //         'base64' => $input['image_logo'],
            //         'modifier_id' => $user->id,
            //         'type' => 'logo',
            //     ]);
            //     $user->allimages()
            //         ->where('modifier_id', $user->id)
            //         ->where('type', 'logo')
            //         ->delete();
            //     $user->allimages()->save($imageable2);
            // }

            // if(!empty($input_client['password'])){
            //     $input_client['password'] = bcrypt($input_client['password']);
            // } else {
            //     $input_client = Arr::except($input_client, array('password'));
            // }
            UserPolyData::set_beamer_data($user_id, $input_client);

            $user->update($input_client);

            $nuser = User::find($user_id);

            $dados = [];
            $dados['user'] = new UserResource($nuser);
            return $this->sendResponse($dados, __('beam.users_update_success'));
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

    public function update_beamer_old(BeamerUpdate $request, $user_id)
    {
        $userid = auth()->user()->id;
        $logs = [
            'user_id'=>$user_id,
            'logged_user_id'=>$userid
        ];
        dblog('updateuser', json_encode($logs));

        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            if (is_null($user)) {
                return $this->sendError( __('beam.users_not_found') );
            }

            $input = $request->all();
            $input_client = $request->only(['name','surname','password', 'phone','website', 'company_name', 'company_doc', 'company_type']);
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
            $input = $request->all();

            $languages = $request->input('my_language');

            // se não estiver vazio grava idiomas
            if(empty($languages)){
                $ar = [['lang_code'=>'fr']];
                $user->save_langs($ar);
            } elseif(is_string($languages)){
                $ar = [['lang_code'=>$languages]];
                $user->save_langs($ar);
            } else {
                $ar = [];
                foreach($languages as $l){
                    $ar[] = ['lang_code'=>$l];
                }
                $user->save_langs($ar);
            }

            $contact_types = ['store'];
            foreach($contact_types as $add_type){
                $to_save = [];
                foreach($input as $chave_val => $val_val){
                    if(strpos($chave_val, $add_type)===0){
                        $k = str_replace($add_type.'_','',$chave_val);
                        $to_save['address_type'] = 'contact';// $add_type;
                        $to_save[$k] = $val_val;
                    }
                }
                // print_r($to_save);
                if(!empty($to_save)){
                    $cols = collect($to_save);
                    $to_latlng = $cols->only('address', 'city', 'country', 'postal_code')->toArray();
                    $wa = new WorldAddress(implode(", ", $to_latlng));
                    $latlng=$wa->request_lat_lng();
                    if(isset($latlng['lat'])){
                        $to_save =  array_merge($to_save, $latlng);
                    }

                    $filtered = $user->address->filter(function ($value, $key) use($add_type) {
                        return $value->address_type == $add_type;
                    });
                    if($filtered->count()==0){
                        $user->address()->create($to_save);
                    } else {
                        $filtered->first()->update($to_save);
                    }
                }
            }

            // grava imagem
            if(!empty($input['image_foto'])){
                $imageable = new ImageModel([
                    'base64' => $input['image_foto'],
                    'modifier_id' => $user->id,
                    'type' => 'profile',
                ]);
                $user->allimages()
                    ->where('modifier_id', $user->id)
                    ->where('type', 'profile')
                    ->delete();
                $user->allimages()->save($imageable);
            }
            // grava logo
            if(!empty($input['image_logo'])){
                $imageable2 = new ImageModel([
                    'base64' => $input['image_logo'],
                    'modifier_id' => $user->id,
                    'type' => 'logo',
                ]);
                $user->allimages()
                    ->where('modifier_id', $user->id)
                    ->where('type', 'logo')
                    ->delete();
                $user->allimages()->save($imageable2);
            }

            // if(!empty($input_client['password'])){
            //     $input_client['password'] = bcrypt($input_client['password']);
            // } else {
            //     $input_client = Arr::except($input_client, array('password'));
            // }

            $user->update($input_client);

            $nuser = User::find($user_id);

            $dados = [];
            $dados['user'] = new UserResource($nuser);
            return $this->sendResponse($dados, __('beam.users_update_success'));
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

    public function update_resource(Request $request, $id)
    {

        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError( __('beam.users_not_found') );
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'role_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }

        if(!empty($input['password'])){
            $input['password'] = bcrypt($input['password']);
        } else {
            $input = Arr::except($input,array('password'));
        }

        $userid = auth()->user()->id;
        $input['modifier_id'] = $userid;

        $user->update($input);

        \DB::table('model_has_roles')->where('model_id',$id)->where('model_type','App\\Models\\User')->delete();

        $user->assignRole($request->input('role_id'));

        DB::table('user_socialmedia')->where('user_id', $id)->delete();
        if(!empty($input['socialmedia'])){
            foreach($input['socialmedia'] as $socialmedia){
                $social = new UserSocialmedia($socialmedia);
                $user->socialmedia()->save($social);
            }
        }

        $dados = [];
        $dados['user'] = new UserResource($user);

        return $this->sendResponse($dados, __('beam.users_update_success'));
    }

    public function destroy($id)
    {

        $user = User::find($id);
        if (is_null($user)) {
            return $this->sendError( __('beam.users_not_found') );
        }

        $userid = auth()->user()->id;
        $user->modifier_id = $userid;
        $user->save();

        $user->delete();

        return $this->sendResponse([], __('beam.users_destroy'));
    }

    /**
    * @OA\Post(
    *     path="/users/{user_id}/video",
    *     tags={"track 🔒"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="video_file",
    *                      type="file",
    *                      format="file",
    *                      description="Max 30 MBytes",
    *                  ),
    *                  @OA\Property(
    *                      property="teaser_text",
    *                      type="string",
    *                  ),
    *                  @OA\Property(
    *                      property="teaser_style",
    *                      type="string",
    *                  ),
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Video uploaded with success",
    *          @OA\JsonContent(
    *                  @OA\Property(
    *                      property="success",
    *                      type="boolean",
    *                  ),
    *                  @OA\Property(
    *                      property="data",
    *                      type="object",
    *                      @OA\Property(
    *                          property="original",
    *                          type="string",
    *                      ),
    *                      @OA\Property(
    *                          property="teaser_text",
    *                          type="string",
    *                      ),
    *                      @OA\Property(
    *                          property="teaser_style",
    *                          type="string",
    *                      ),
    *                      @OA\Property(
    *                          property="file_url_to_download",
    *                          type="string",
    *                      ),
    *                  ),
    *                  @OA\Property(
    *                      property="messages",
    *                      type="string"
    *                  )
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="Upload problem encountered",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function video(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        if($userid!=$user_id){
            return $this->sendError( __('beam.user_not_allowed') );
        }
        $input = $request->all();

        $validator = Validator::make($input, [
            'video_file' => 'required|mimes:mp4,mov|max:40971520',
        ]);

        if($validator->fails()){
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }

        $video_file = $request->file('video_file');

        $st_video_file = generateRandomString() . '.' . $video_file->extension();

        $storage_name = 'storage_tmp/' . $st_video_file;

        $destinationPath = storage_path('storage_tmp');
        $video_path = $video_file->move($destinationPath, $st_video_file);

        $dados = [
            'user_id' => $userid,
            'original'  => $st_video_file,
            'teaser_text' => $request->get('teaser_text'),
            'teaser_style' => $request->get('teaser_style','{}'),
        ];
        $video = UserVideofeed::create($dados);

        dblog('video upload', $st_video_file);
        ProcessVideo::dispatch($video)->onQueue('videos');

        $dados['file_url_to_download'] = route('url_video', $video->id);

        // UserVideofeed::where('id','!=',$video->id)->where('user_id', $user_id)->delete();

        return $this->sendResponse($dados, __('beam.user_videofeed') );
    }


    public function eraseaccount(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        $logs = [
            'user_id'=>$user_id,
            'logged_user_id'=>$userid
        ];
        dblog('eraseaccount', json_encode($logs));
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            if (is_null($user)) {
                return $this->sendError( __('beam.users_not_found') );
            }
            auth()->user()->currentAccessToken()->delete();
            $user->eraseAccount();
            $dados= [];
            return $this->sendResponse($dados, __('beam.users_update_success'));
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }


/**
    * @OA\Get(
    *     path="/user/getcard/{hash}",
    *     description="Get a user info. The route /api/share/{hash} returns the same response",
    *     tags={"users"},
    *     @OA\Parameter(
    *         name="hash",
    *         description="A beamer hash or beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Response(
    *          response="200",
    *          description="Get list of bearmer",
    *          @OA\JsonContent(
    *              ref="#/components/schemas/basepins"
    *          ),
    *     )
    * )
    */
    public function getShare(Request $request, $hash)
    {
        $users = User::whereNull('uuid')
               ->get();

        foreach ($users as $user) {
            echo $user->setUuid();
        }
        $user = \DB::table('users')
            ->where('uuid', $hash)
            ->orWhere('id', $hash)
            ->first();

        if(empty($user->id)){
            $response = [
                'success' => true,
                'data'    => [
                    'pins' => []
                ]
            ];
            return response()->json($response, 200);
        } else {
            $local = Beamer::beamer_by_id($user->id);
            if($local->count() == 0){
                $local = Beamer::beamer_empty_by_id($user->id);
            }
            $response = [
                'success' => true,
                'data'    => [
                    'pins' => PinsResource::collection($local)
                ]
            ];
            return response()->json($response, 200);
        }
    }

/**
    * @OA\Get(
    *     path="/profile/{hash}",
    *     description="Get a beamer profile.",
    *     summary="Does not require authentication",
    *     tags={"beamer"},
    *     @OA\Parameter(
    *         name="hash",
    *         description="A beamer uuid or beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
   *     @OA\Response(
    *          response="200",
    *          description="Get beamer profile",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="profile",
    *                      type="object",
    *                      ref="#/components/schemas/profile"
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    * )
    */
    public function get_profile(Request $request, $hash)
    {
        $user = auth()->user();
        if(!empty($user->id)){
            ProfileResource::$logged_userid = $user->id;
        }

        $prof_obj = $this->__get_profile($hash);

        if(empty($prof_obj)){
            $response = [
                'success' => true,
                'data'    => [
                    'profile' => []
                ]
            ];
            return response()->json($response, 404);
        } else {
            $response = [
                'success' => true,
                'data'    => [
                    'profile' => $prof_obj
                ]
            ];
            return response()->json($response, 200);
        }
    }

    public function __get_profile($hash)
    {
        $chave = "ch2" . md5($hash);
        if(Cache::has($chave)){
            return Cache::get($chave);
        }
        $user = \DB::table('users')
            ->where('uuid', $hash)
            ->orWhere('id', $hash)
            ->first();

        if(empty($user->id)){
            $resu = [];
        } else {
            $local = Beamer::beamer_by_id($user->id);
            if($local->count() == 0){
                $local = Beamer::beamer_empty_by_id($user->id);
            }
            $resu = new ProfileResource($local->first());
        }
        Cache::put($chave, $resu, 300);
        return $resu;
    }

/**
    * @OA\Get(
    *     path="/profilemany",
    *     description="Get severals beamer profiles.",
    *     summary="Does not require authentication",
    *     tags={"beamer"},
   *     @OA\Response(
    *          response="200",
    *          description="Get severals beamer profiles",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="manyprofile",
    *                      type="array",
    *                      @OA\Items( ref="#/components/schemas/profile" )
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    * )
    */
    public function get_profilemany(Request $request)
    {
        $ids = $request->get('users_ids');
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $profiles = [];
        foreach($ids as $id) {
            $pro = $this->__get_profile($id);
            $profiles[$id] = $pro;
        }

        $response = [
            'success' => true,
            'data'    => [
                'manyprofile' => $profiles
            ]
        ];
        return response()->json($response, 200);
    }

/**
    * @OA\Post(
    *     path="/users/{user_id}/quick_on",
    *     description="Beamber turns on line in one click",
    *     tags={"track 🔒"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="lat",
    *                      type="number",
    *                  ),
    *                  @OA\Property(
    *                      property="lng",
    *                      type="number",
    *                  ),
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Sumary of user properties",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="user",
    *                      type="object",
    *                      ref="#/components/schemas/simpleuser"
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="user_id incompatible with the  param user_id",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function quick_on(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            if($user->quick_on==false){
                return $this->sendError( __('beam.user_not_allowed') );
            }

            $user->position = 'on_line';
            $user->save();
            UserTrack::where('user_id', $user_id)->update(['last_one' => 0]);
            UserTrack::where('user_id', $user_id)->delete();


            $datas = [
                'lat'=>$request->get('lat'),
                'lng'=>$request->get('lng')
            ];
            $user_track_obj = UserTrack::copy_last_track($user_id, $datas);

            if($user_track_obj===false){
                return $this->sendError( __('beam.user_not_allowed') );
            }
            // trigger event to map
            // Trigger the event
            // event(new MapChanged($user_track_obj));

            $nuser = User::find($user_id);
            $dados = [];
            $dados['user'] = new UserSimpleResource($nuser);
            return $this->sendResponse($dados, __('beam.users_show') );
            }
        return $this->sendError( __('beam.user_not_allowed') );
    }

/**
    * @OA\Post(
    *     path="/users/{user_id}/quick_off",
    *     description="Beamber turns off line",
    *     tags={"track 🔒"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A user id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Response(
    *          response="200",
    *          description="User end track successfully",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/base",
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="user_id or beamer id not recognized",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function quick_off(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            $user->position = 'off_line';
            $user->save();
            $track_atual = UserTrack::where('user_id', $user_id)->withTrashed()->first();
            $beamer_type = $track_atual->beamer_type;
            $track_atual->status = 'end';
            $track_atual->save();

            UserTrack::remove_pin($user_id);

            UserTracking::create([
                'user_id'=>$user_id,
                'status'=>'end',
                'beamer_type' => $beamer_type,
                'lat'=>$request->get('lat'),
                'lng'=>$request->get('lng'),
            ]);

            UserTracking::where('user_id', $user_id)->delete();
            $dados = [];
            return $this->sendResponse($dados,'');
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

/**
    * @OA\Get(
    *     path="/users/{user_id}/switch_to/{mode}",
    *     description="Beamber switch to line in one click",
    *     tags={"register"},
    *     @OA\Parameter(
    *         name="user_id",
    *         description="A beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="mode",
    *         description="Should be beamer or client",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Response(
    *          response="200",
    *          description="Sumary of user properties",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="user",
    *                      type="object",
    *                      ref="#/components/schemas/simpleuser"
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *          response="400",
    *          description="user_id incompatible with the  param user_id",
    *          @OA\JsonContent(
    *            ref="#/components/schemas/baseerror",
    *          ),
    *     ),
    * )
    */
    public function switch_to(Request $request, $user_id, $mode = 'beamer')
    {
        $userid = auth()->user()->id;
        // only edit yourself
        if($userid==$user_id){
            $user = User::find($user_id);
            $user->interface_as = $mode;
            $user->position = 'off_line';
            $user->save();

            UserTrack::force_end_track($user_id);

            $nuser = User::find($user_id);
            $dados = [];
            $dados['user'] = new UserSimpleResource($nuser);
            return $this->sendResponse($dados, __('beam.users_show') );
        }
        return $this->sendError( __('beam.user_not_allowed') );
    }

}
