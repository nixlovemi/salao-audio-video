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

    Route::prefix('people')->group(function () {
        Route::get('/', 'App\Http\Controllers\People@index')->name('people.index');
        Route::get('/view/{codedId}', 'App\Http\Controllers\People@view')->name('people.view');
        Route::get('/add', 'App\Http\Controllers\People@add')->name('people.add');
        Route::post('/doAdd', 'App\Http\Controllers\People@doAdd')->name('people.doAdd');
        Route::get('/edit/{codedId}', 'App\Http\Controllers\People@edit')->name('people.edit');
        Route::post('/doEdit', 'App\Http\Controllers\People@doEdit')->name('people.doEdit');
    });

    Route::prefix('attendance')->group(function () {
        Route::get('/', 'App\Http\Controllers\Attendance@index')->name('attendance.index');
        Route::get('/view/{timestamp}', 'App\Http\Controllers\Attendance@view')->name('attendance.view');
        Route::get('/add', 'App\Http\Controllers\Attendance@add')->name('attendance.add');
        Route::post('/doAdd', 'App\Http\Controllers\Attendance@doAdd')->name('attendance.doAdd');
        Route::get('/edit/{timestamp}', 'App\Http\Controllers\Attendance@edit')->name('attendance.edit');
        Route::post('/doEdit', 'App\Http\Controllers\Attendance@doEdit')->name('attendance.doEdit');
        Route::post('/ajaxFilterTable', 'App\Http\Controllers\Attendance@ajaxFilterTable')->name('attendance.ajaxFilterTable');
    });
});
