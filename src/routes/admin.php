<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\StampCorrectionRequestController;

//管理者ログイン画面
Route::get('/admin/login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'login']);
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth','is_admin'])->group(function () {
    //勤怠一覧画面
    Route::get('/admin/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    //勤怠詳細画面
    Route::get('/admin/attendance/detail/{id}',[AttendanceController::class,'detail'])->name('attendance.detail');
    Route::put('/admin/attendance/detail/{id}',[AttendanceController::class,'update'])->name('attendance.update');
    //スタッフ一覧画面
    Route::get('/admin/staff/list',[AttendanceController::class,'staffList'])->name('staff.list');
    //スタッフ別勤怠一覧画面
    Route::get('/admin/attendance/staff/{id}',[AttendanceController::class,'attendanceStaff'])->name('attendance.staff');
    //申請一覧画面
    Route::get('/admin/stamp_correction_request/list',[StampCorrectionRequestController::class,'requestList'])->name('stamp_correction_request.list');
});