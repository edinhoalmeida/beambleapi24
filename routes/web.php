<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TesteController;
use App\Http\Controllers\ApidocController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\InboxController;



use App\Http\Controllers\StripePaymentController;


use App\Http\Controllers\Web\BaseController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\BeamerController;
use App\Http\Controllers\Web\WebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/teste', [TesteController::class, 'index']);

Route::get('/', function () {
    $response = [
        'success' => true,
        'message' => "SERVER ON",
    ];
    return response()->json($response, 200);
} )->name('home');


Route::get('apidoc', [ApidocController::class,'index']);

// Route::get('apidoc/v2', function () {
//     return view('apidoc.v2.index');
// });
Route::get('apidoc/v2', function () {
    return view('apidoc.v2.index2');
});

Route::get('stripe', [StripePaymentController::class, 'stripe']);
Route::post('stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');


Route::get('mediatest', [TesteController::class, 'mediatest']);
Route::get('babel/{text}/{text_lang}/{target_lang}', [TesteController::class, 'babel']);
Route::get('babelt2s/{text}/{text_lang}', [TesteController::class, 'babel_t2s']);


// rotas de login e cadastro
Route::get('/login', [BaseController::class, 'login'])->name('web.login');
Route::get('/logout', [BaseController::class, 'logout'])->name('web.logout');
Route::post('/authenticate', [BaseController::class, 'authenticate'])->name('web.authenticate');


Route::get('search', [BaseController::class, 'search'])->name('web.search');
Route::get('beamer/{id}', [BaseController::class, 'beamer_view'])->name('web.beamer_view')->where(['id' => '[0-9]+']);
Route::get('beamerinbox/{id}', [ClientController::class, 'inbox'])->name('web.inbox')->where(['id' => '[0-9]+']);

Route::get('/beamer_register', [WebController::class, 'beamer_register'])->name('web.beamer_register');
Route::get('/client_register', [WebController::class, 'client_register'])->name('web.client_register');


Route::get('/dashboard', [WebController::class, 'dashboard'])->name('web.dashboard');
Route::get('/settings', [WebController::class, 'settings'])->name('web.settings');
Route::post('/settings', [WebController::class, 'save_settings'])->name('web.save_settings');


Route::get('/apiweb/inbox/{client_id}/{beamer_id}',  [InboxController::class, 'messages'])->where(['client_id' => '[0-9]+', 'beamer_id' => '[0-9]+']);



Route::get('/charge', [ClientController::class, 'charge'])->name('web.charge');
Route::post('/charge', [ClientController::class, 'stripePost'])->name('web.charge.post');

Route::get('/connect', [ClientController::class, 'connect'])->name('web.connect');
Route::post('/connect', [ClientController::class, 'connectPost'])->name('web.connect.post');

Route::get('/subscription', [ClientController::class, 'subscription'])->name('web.subscription');
Route::post('/subscription', [ClientController::class, 'subscriptionPost'])->name('web.subscription.post');


// Route::controller(StripePaymentController::class)->group(function(){
//     Route::get('stripe', 'stripe');
//     Route::post('stripe', 'stripePost')->name('stripe.post');
// });

// Route::get('resizeImage', [ImageController::class, 'resizeImage']);
// Route::post('resizeImagePost', [ImageController::class, 'ImagePost'])->name('resizeImagePost');
