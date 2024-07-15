<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Team\BaseController;
use App\Http\Controllers\Team\TeamController;
use App\Http\Controllers\Team\WebviewController;
use App\Http\Controllers\Team\WvtextsController;
use App\Http\Controllers\Team\WvfaqsController;
use App\Http\Controllers\Team\WvcontactsController;
use App\Http\Controllers\Team\WvusersController;


/*
| -------------------------------------------------------------------------
| Team ROUTES
| ------------------------------------------------------------------------
*/
$domai = config('app.url');
if(strpos($domai,'team.')===0){
    Route::domain($domai)->group(function () {

        Route::get('/', [BaseController::class, 'login'])->name('team.login');
        Route::get('/login', [BaseController::class, 'login'])->name('team.login');
        Route::get('/logout', [BaseController::class, 'logout'])->name('team.logout');
        Route::post('/authenticate', [BaseController::class, 'authenticate'])->name('team.authenticate');

        Route::get('/dashboard2', [TeamController::class, 'dashboard2'])->name('team.dashboard2');

        Route::get('/dashboard', [TeamController::class, 'dashboard'])->name('team.dashboard');

        Route::get('/map', [TeamController::class, 'map'])->name('team.map');
        Route::get('/users', [TeamController::class, 'users'])->name('team.users');
        // Route::get('/contacts', [TeamController::class, 'contacts'])->name('team.contacts');


        Route::get('/params', [TeamController::class, 'params'])->name('team.params');
        Route::post('/params', [TeamController::class, 'save_params'])->name('team.save_params');

        Route::get('/profile', [TeamController::class, 'profile'])->name('team.profile');
        Route::post('/profile', [TeamController::class, 'save_profile'])->name('team.save_profile');


        Route::get('/contacts', [WvcontactsController::class, 'index'])->name('team.contacts');
        Route::get('/newusers', [WvcontactsController::class, 'newusers'])->name('team.newusers');


        // webview routes
        Route::get('/wv/test', [WebviewController::class, 'test'])->name('webview.test');
        Route::any('/wv/onboarding', [WebviewController::class, 'onboarding'])->name('webview.onboardingapp');
        Route::post('/wv/onboardingtest', [WebviewController::class, 'onboardingtest'])->name('webview.onboarding');
        Route::get('/wv/{accounttk}/RNTSz4EwXCLM', [WebviewController::class, 'onboardingByToken'])->name('webview.onboarding-token');
        Route::get('/wv/{accounttk}/xgrvlVfzz5jJ', [WebviewController::class, 'onboardingTestByToken'])->name('webview.onboarding-test');
        Route::get('/wv/onboarding-success', [WebviewController::class, 'onboardingSuccess'])->name('webview.onboarding-success');

        // crud text
        Route::resource('/texts', WvtextsController::class, ['names' => 'wvtexts']);
        Route::resource('/faqs', WvfaqsController::class, ['names' => 'wvfaqs']);
        Route::resource('/users', WvusersController::class, ['names' => 'wvusers']);

        Route::post('/users-shopper', [WvusersController::class, 'users_shopper'])->name('wvusers.shopper');
 
        Route::middleware([App\Http\Middleware\Cors::class])->group(function () {
                // pages:
            Route::get('/wv/page/{page_slug}', [WebviewController::class, 'page'])->name('webview.pages');
 
            Route::get('/wv/contact', [WebviewController::class, 'contact'])->name('webview.contact');
            Route::post('/wv/contact-save', [WebviewController::class, 'contactSave'])->name('webview.contact-save');
        });


    });
}