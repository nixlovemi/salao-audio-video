<?php

use Illuminate\Support\Facades\Route;

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

// ==============================================
// ALL ROUTES MUST HAVE NAME FOR PERMISSION CHECK
// ==============================================
Route::group([], function(){
    Route::fallback(function () {
        // session()->flush();
        echo '404'; die;
        return view('404');
    })->name('site.404');
});

Route::get('/', 'App\Http\Controllers\Login@index')->name('site.login');
Route::post('/doLogin', 'App\Http\Controllers\Login@doLogin')->name('site.doLogin');

// ================================================
// ADD ROUTE PERMISSIONS ON App\Helpers\Permissions
// ================================================
Route::middleware(['authWeb'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\Dashboard@index')->name('site.dashboard');
});
