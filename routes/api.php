<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\MapsController;

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\InboxController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CallController;
use App\Http\Controllers\API\Call2Controller;
use App\Http\Controllers\API\LangController;
use App\Http\Controllers\API\AudioController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ShippingController;
use App\Http\Controllers\API\ShopperController;
use App\Http\Controllers\API\MessagingController;
use App\Http\Controllers\API\CatalogController;

use App\Http\Controllers\Team\WebviewController;

use App\Http\Controllers\API\FollowController;

use App\Http\Middleware\OptionalAuthSanctum;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// headers: {
//     'Content-Type': 'application/json',
//     'Authorization': 'Bearer '+TOKEN
// }

// Public
Route::get('settings',  [SettingsController::class, 'get_settings'] );
Route::post('worldaddress',  [AddressController::class, 'get_fmt'] );

Route::any('search_beamer', [MapsController::class, 'search_beamer'])->middleware(OptionalAuthSanctum::class);
Route::any('search_beamer_summary', [MapsController::class, 'search_beamer_summary'])->middleware(OptionalAuthSanctum::class);
Route::any('search_by_words', [MapsController::class, 'searchBeamerByWords'])->middleware(OptionalAuthSanctum::class);
Route::any('search_to_feed', [MapsController::class, 'searchToFeed'])->middleware(OptionalAuthSanctum::class);

Route::post('register', [RegisterController::class, 'register']);
Route::post('change_passSWED', [RegisterController::class, 'change_pass']);

Route::post('login', [RegisterController::class, 'login']);
Route::get('login', [RegisterController::class, 'login'])->name('login');

// Route::post('register/reset', 'API\RegisterController@setnewpassword')->name('setnewpassword');
// Route::get('register/reset/{code}', 'API\RegisterController@reset')->name('forget');
Route::post('register/passwordreset',  [RegisterController::class, 'forget'] );
Route::get('register/passwordreset',  [RegisterController::class, 'forget'] );
Route::get('register/reset/{code}',  [RegisterController::class, 'reset'] )->name('forget');

Route::post('register/newotp',  [RegisterController::class, 'new_otp'] );
Route::post('register/verifyotp',  [RegisterController::class, 'verify_otp'] );
Route::get('register/templateotp',  [RegisterController::class, 'templateotp'] );

Route::post('register_all', [RegisterController::class, 'register_all'])->name('api.register_all');
Route::post('already_registered', [RegisterController::class, 'already_registered'])->name('api.already_registered');

Route::post('register_generic', [RegisterController::class, 'register_generic'])->name('api.register_generic');

Route::post('register_client_simple', [RegisterController::class, 'register_client_simple'])->name('api.register_client_simple');
Route::post('register_client', [RegisterController::class, 'register_client'])->name('api.register_client');
Route::post('register_beamer', [RegisterController::class, 'register_beamer'])->name('api.register_beamer');



// unprotected
Route::get('chat/getaudio/{audio_name}', [AudioController::class, 'audio'])->name('url_audio');
Route::get('user/getvideo/{video_name}', [VideoController::class, 'video'])->name('url_video');
Route::get('user/getthumb/{thumb_name}', [VideoController::class, 'thumb'])->name('url_thumb');
Route::get('i/{user_id}', [ImageController::class, 'get'])->name('url_image');
Route::get('l/{user_id}', [ImageController::class, 'getl'])->name('url_logo');
Route::get('p/{product_id}', [ImageController::class, 'getp'])->name('url_product');

// contacts
Route::post('contact-save', [WebviewController::class, 'contactSave'])->name('webview.contact-save');

// stripe webhook
Route::post('AE6aOCEOw1BnP6vr/iJwgiM2CY4sszlUh',  [PaymentController::class, 'postWebhook']);

// uber webhook
// https://apibb.beamble.com/api/Rl4zEqFFxKnuWHSS/xeX7ctqnJMtIFdqf
Route::post('Rl4zEqFFxKnuWHSS/xeX7ctqnJMtIFdqf',  [ShippingController::class, 'postWebhook']);
Route::get('Rl4zEqFFxKnuWHSS',  [ShippingController::class, 'test']);

// api to log video batch conversions
Route::post('FFxKnuWRl4zEqHSS',  [WebviewController::class, 'video_batch_debug']);



Route::post('messaging/all',  [MessagingController::class, 'all']);
Route::post('messaging/one',  [MessagingController::class, 'one']);


Route::get('test',  [CallController::class, 'test']);

Route::get('share/{hash}',  [UserController::class, 'getShare'])->name('share_user');
Route::get('user/getcard/{hash}',  [UserController::class, 'getShare']);
Route::get('profile/{hash}',  [UserController::class, 'get_profile'])->middleware(OptionalAuthSanctum::class);
Route::post('profilemany',  [UserController::class, 'get_profilemany'])->middleware(OptionalAuthSanctum::class);

Route::get('user/status', [UserController::class, 'status'])->middleware(OptionalAuthSanctum::class);

Route::middleware('auth:sanctum')->group( function () {

    Route::get('inbox/{client_id}/{beamer_id}',  [InboxController::class, 'messages'])->where(['client_id' => '[0-9]+', 'beamer_id' => '[0-9]+']);

    Route::get('users/{user_id}/edit/{interface_as?}', [UserController::class, 'edit']);
    Route::put('users/{user_id}/update_client', [UserController::class, 'update_client']);
    Route::post('users/{user_id}/update_client', [UserController::class, 'update_client']);
    Route::put('users/{user_id}/update_beamer', [UserController::class, 'update_beamer']);
    Route::post('users/{user_id}/update_beamer', [UserController::class, 'update_beamer']);
    Route::get('users/{user_id}/switch_to/{mode}', [UserController::class, 'switch_to']);


    Route::post('users/{user_id}/eraseaccount', [UserController::class, 'eraseaccount']);

    Route::post('users/{user_id}/video', [UserController::class, 'video']);
    Route::post('users/{user_id}/start_track', [UserController::class, 'start_track']);
    Route::post('users/{user_id}/quick_on', [UserController::class, 'quick_on']);
    Route::post('users/{user_id}/quick_off', [UserController::class, 'quick_off']);
    Route::post('users/{user_id}/end_track', [UserController::class, 'end_track']);
    Route::post('users/{user_id}/update_track', [UserController::class, 'update_track']);

    // roda como beamer
    Route::get('calls/{user_id}', [CallController::class, 'checkcall']);
    // roda como client
    Route::post('calls/{user_id}/{beamer_id}/ask', [CallController::class, 'askcall']);
    // roda como beamer
    Route::post('calls/{call_id}/accept', [CallController::class, 'acceptcall']);
    // roda como beamer
    Route::post('calls/{call_id}/reject', [CallController::class, 'rejectcall']);

    // roda como client
    Route::post('calls/{call_id}/accepted', [CallController::class, 'acceptedcall']);


    // roda como client ou beamer
    Route::post('calls/{call_id}/savelog', [CallController::class, 'savelog']);

    Route::get('calls/{call_id}/status', [CallController::class, 'status']);

    // roda como client ou beamer
    Route::post('calls/{call_id}/rating', [CallController::class, 'rating']);

    // start timer of call to freelancer beamer type
    Route::get('calls/{call_id}/timer/start', [CallController::class, 'timer_start']);
    Route::get('calls/{call_id}/timer/accept', [CallController::class, 'timer_accept']);
    Route::get('calls/{call_id}/timer/reject', [CallController::class, 'timer_reject']);

    Route::get('calls/history/{filter}', [Call2Controller::class, 'gethistory']);

    // roda como client ou beamer
    Route::post('chat/text2text', [LangController::class, 'text2text']);
    Route::post('chat/text2speech', [LangController::class, 'text2speech']);
    Route::post('chat/speech2text', [LangController::class, 'speech2text']);


    // ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']

    // Route::resource('users',  UserController::class);

    Route::post('me', [UserController::class, 'me']);
    Route::get('logout',  [UserController::class, 'logout']);


    Route::get('payments/customer',  [PaymentController::class, 'getCustomer']);
    Route::get('payments/ephemeralkey',  [PaymentController::class, 'createEphemeralKey']);
    Route::post('payments/paymentintent',  [PaymentController::class, 'createPaymentIntent']);
    Route::get('payments/paymentintent/guarantee',  [PaymentController::class, 'createPaymentGuarantee']);
    Route::post('payments/success',  [PaymentController::class, 'paymentIntentSuccess']);

    Route::post('payments/paycall',  [PaymentController::class, 'makePayCall']);

    Route::post('shopper/{call_id}/product/add',  [ShopperController::class, 'add']);
    Route::get('shopper/{call_id}/products/new',  [ShopperController::class, 'listNew']);
    Route::get('shopper/{call_id}/products/all',  [ShopperController::class, 'listAll']);
    Route::post('shopper/{call_id}/checkout',  [ShopperController::class, 'checkout']);
    Route::get('shopper/beamer/catalog',  [ShopperController::class, 'listCatalog']);

    Route::get('catalog',  [CatalogController::class, 'list']);
    Route::post('catalog/add',  [CatalogController::class, 'add']);
    Route::delete('catalog/delete/{product_id}',  [CatalogController::class, 'delete']);
    Route::post('catalog/update/{product_id}',  [CatalogController::class, 'update']);



    Route::get('shopper/{call_id}/{product_id}/confirm',  [ShopperController::class, 'confirm'])->where('call_id', '[0-9]+')->where('product_id', '[0-9]+');
    Route::get('shopper/{call_id}/{product_id}/reject',  [ShopperController::class, 'reject'])->where('call_id', '[0-9]+')->where('product_id', '[0-9]+');
    Route::get('shopper/{call_id}/{product_id}/read',  [ShopperController::class, 'product_read'])->where('call_id', '[0-9]+')->where('product_id', '[0-9]+');

    Route::post('shopper/{call_id}/clientdetails',  [ShopperController::class, 'clientDetails']);

    // // Route::resource('perfis',  RoleController::class);
    // Route::get('perfis',  [RoleController::class, 'index']);
    // Route::get('perfis/create',  [RoleController::class, 'create']);

    // Route::get('perfis/create',  [RoleController::class, 'create']);

    // Route::get('perfis/ajusta_permissao_perfil_admin',  [RoleController::class, 'ajusta_permissao_perfil_admin']);

    // Route::get('perfis/{id}',  [RoleController::class, 'show'])->where(['id' => '[0-9]+']);
    // Route::post('perfis',  [RoleController::class, 'store']);
    // Route::put('perfis/{id}',  [RoleController::class, 'update'])->where(['id' => '[0-9]+']);
    // Route::delete('perfis/{id}',  [RoleController::class, 'destroy'])->where(['id' => '[0-9]+']);


    // Route::post('saveimagens', [ImageController::class, 'save']);


    // VERSION 2
    Route::get('follow/{beamer_id}', [FollowController::class, 'beamer']);
});
