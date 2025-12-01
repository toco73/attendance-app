<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

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

Route::get('/', function () {
    return redirect('/login');
});

//ログイン画面
Route::get('/login',[AuthController::class,'showLoginForm'])->name('login');
Route::post('/login',[AuthController::class,'login']);

//メール認証機能
Route::get('/email/verify',function(){
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); 
    return redirect()->route('attendance.index'); 
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//認証メール再送機能
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->intended(RouteServiceProvider::HOME);
    }
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::middleware('auth','verified')->group(function(){
    //勤怠登録画面
    Route::get('/attendance',[AttendanceController::class,'index'])->name('attendance.index');
    Route::post('/attendance',[AttendanceController::class,'updateStatus'])->name('attendance.updateStatus');
    //勤怠一覧画面
    Route::get('/attendance/list',[AttendanceController::class,'list'])->name('attendance.list');
    //勤怠詳細画面
    Route::get('/attendance/detail/{id}',[AttendanceController::class,'detail'])->name('attendance.detail');
    Route::post('/attendance/detail/{id}',[AttendanceController::class,'updateRequest'])->name('attendance.updateRequest');
    //申請一覧画面
    Route::get('/stamp_correction_request/list',[StampCorrectionRequestController::class,'list'])->name('stamp_correction_request.list');
});
